<?php

namespace App\Services;

use App\Jobs\SendCampaignBatchJob;
use App\Models\AdminSettings;
use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\EmailSuppression;
use App\Models\Photoshoot;
use App\Models\WeeklyDigestHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WeeklyPhotoshootDigestService
{
    /**
     * Compute the standardized reporting period: Sunday 00:00:00 through Monday 23:59:59.
     */
    public static function getWeeklyDateRange(?Carbon $referenceDate = null): array
    {
        $timezone = config('app.timezone', 'UTC');
        $now = $referenceDate ? $referenceDate->copy()->timezone($timezone) : Carbon::now($timezone);

        // Find the most recently completed Monday 23:59:59
        if ($now->isMonday()) {
            // If running on Monday, take the previous week's Monday
            $end = $now->copy()->subWeek()->endOfWeek(Carbon::MONDAY)->endOfDay();
        } else {
            // Find the most recent Monday passed
            $end = $now->copy()->previous(Carbon::MONDAY)->endOfDay();
        }

        // Sunday starting 8 days before that Monday
        $start = $end->copy()->subDays(8)->startOfDay();

        $periodKey = $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d');
        $label = $start->format('M d') . ' - ' . $end->format('M d, Y');

        return [
            'start' => $start,
            'end' => $end,
            'period_key' => $periodKey,
            'label' => $label,
        ];
    }

    /**
     * Query newly published, publicly accessible photoshoots within the weekly period.
     */
    public static function getEligiblePhotoshoots(Carbon $start, Carbon $end, bool $excludePastDigests = true): Collection
    {
        $query = Photoshoot::whereBetween('created_at', [$start, $end])
            ->where('prompts_count', '>', 0)
            ->whereNotNull('slug')
            ->where('slug', '!=', '');

        if ($excludePastDigests) {
            $pastIds = WeeklyDigestHistory::where('status', 'dispatched')
                ->whereNotNull('photoshoot_ids')
                ->pluck('photoshoot_ids')
                ->flatten()
                ->filter()
                ->unique()
                ->toArray();

            if (!empty($pastIds)) {
                $query->whereNotIn('id', $pastIds);
            }
        }

        return $query->with([
                'category',
                'images' => function ($q) {
                    $q->where('status', 'active')->orderBy('id', 'asc');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('id');
    }

    /**
     * Render the inner HTML body of the photoshoot digest.
     */
    public static function renderDigestBody(Collection $photoshoots, array $dateRange): string
    {
        $headline = $photoshoots->count() === 1
            ? 'New Commercial Studio Photoshoot'
            : 'New Commercial Studio Photoshoots (' . $photoshoots->count() . ' Sets)';

        return view('emails.components.weekly-photoshoot-digest-body', [
            'photoshoots' => $photoshoots,
            'periodLabel' => $dateRange['label'],
            'headline' => $headline,
        ])->render();
    }

    /**
     * Execute the full weekly photoshoot digest dispatch with controlled batching.
     */
    public static function run(
        bool $dryRun = false,
        bool $force = false,
        int $batchSize = 50,
        int $batchDelaySeconds = 15,
        ?Carbon $referenceDate = null
    ): array {
        // 1. Emergency Kill-Switch Guard
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            Log::warning('Weekly photoshoot digest skipped: Emergency bulk email kill-switch is active.');
            return [
                'success' => false,
                'status' => 'kill_switch_engaged',
                'message' => 'Emergency bulk email kill-switch is currently active. Dispatch halted.',
            ];
        }

        // 2. Date Range
        $range = self::getWeeklyDateRange($referenceDate);

        // 3. Idempotency Check (prevent duplicate sends for the same week)
        $alreadySent = WeeklyDigestHistory::where('period_key', $range['period_key'])
            ->where('status', 'dispatched')
            ->exists();

        if ($alreadySent && !$force) {
            return [
                'success' => false,
                'status' => 'already_dispatched',
                'period_key' => $range['period_key'],
                'period_label' => $range['label'],
                'message' => "Weekly digest for {$range['label']} has already been dispatched. Use --force to override.",
            ];
        }

        // 4. Photoshoot Selection
        $photoshoots = self::getEligiblePhotoshoots($range['start'], $range['end'], !$force);

        if ($photoshoots->isEmpty()) {
            if (!$dryRun) {
                WeeklyDigestHistory::updateOrCreate(
                    ['period_key' => $range['period_key']],
                    [
                        'period_start' => $range['start'],
                        'period_end' => $range['end'],
                        'photoshoots_count' => 0,
                        'photoshoot_ids' => [],
                        'recipients_count' => 0,
                        'status' => 'skipped_no_photoshoots',
                    ]
                );
            }

            return [
                'success' => true,
                'status' => 'skipped_no_photoshoots',
                'period_key' => $range['period_key'],
                'period_label' => $range['label'],
                'photoshoots_count' => 0,
                'photoshoot_titles' => [],
                'recipients_count' => 0,
                'batch_size' => $batchSize,
                'batch_delay_seconds' => $batchDelaySeconds,
                'total_batches' => 0,
                'estimated_duration_seconds' => 0,
                'message' => "No newly published photoshoots found between {$range['label']}. Digest skipped gracefully.",
            ];
        }

        // 5. Audience Resolution (channel-aware suppression & disposable exclusion)
        $rawAudience = AudienceService::getDeliverableMarketingAudience();
        $suppressed = EmailSuppression::where('channel_scope', 'all')
            ->orWhere(function ($q) {
                $q->where('channel_scope', 'marketing_only');
            })
            ->pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->flip();

        $audience = $rawAudience->filter(function ($recipient) use ($suppressed) {
            $email = strtolower(trim($recipient->email));
            return !empty($email) && !isset($suppressed[$email]);
        });

        $totalRecipients = $audience->count();
        $totalPhotoshoots = $photoshoots->count();
        $totalBatches = (int) ceil($totalRecipients / max(1, $batchSize));
        $estimatedDurationSeconds = max(0, ($totalBatches - 1) * $batchDelaySeconds);

        if ($dryRun) {
            return [
                'success' => true,
                'status' => 'dry_run_simulation',
                'period_key' => $range['period_key'],
                'period_label' => $range['label'],
                'photoshoots_count' => $totalPhotoshoots,
                'photoshoot_titles' => $photoshoots->pluck('title')->toArray(),
                'recipients_count' => $totalRecipients,
                'batch_size' => $batchSize,
                'batch_delay_seconds' => $batchDelaySeconds,
                'total_batches' => $totalBatches,
                'estimated_duration_seconds' => $estimatedDurationSeconds,
                'message' => "DRY RUN: {$totalPhotoshoots} photoshoots would be sent to {$totalRecipients} subscribers across {$totalBatches} batches.",
            ];
        }

        if ($totalRecipients === 0) {
            return [
                'success' => false,
                'status' => 'no_eligible_recipients',
                'message' => 'No eligible deliverable subscribers found for marketing channel.',
            ];
        }

        // 6. Generate Dynamic Subject Line
        $firstTitle = $photoshoots->first()->title;
        if ($totalPhotoshoots === 1) {
            $subject = "📸 New Studio Photoshoot: {$firstTitle}";
        } else {
            $remaining = $totalPhotoshoots - 1;
            $subject = "📸 New Photoshoots: {$firstTitle} + {$remaining} More Sets";
        }

        // 7. Render Inner Content
        $contentHtml = self::renderDigestBody($photoshoots, $range);

        // 8. Create EmailCampaign Record
        $campaign = EmailCampaign::create([
            'name' => "Weekly Photoshoot Drop: {$range['label']}",
            'subject' => $subject,
            'preview_text' => "Explore this week's newly published commercial studio photoshoots and multi-angle prompts.",
            'target_segment' => 'all_verified',
            'template_preset' => 'weekly_photoshoot_drop',
            'content' => $contentHtml,
            'status' => 'sending',
            'recipients_count' => $totalRecipients,
            'track_opens' => 1,
            'track_clicks' => 1,
            'scheduled_at' => now(),
            'sent_at' => now(),
            'created_by' => auth()->id() ?? null,
        ]);

        // 9. Bulk Seed CampaignRecipients
        $recipientRows = [];
        $nowTimestamp = now();

        foreach ($audience as $user) {
            $recipientRows[] = [
                'campaign_id' => $campaign->id,
                'recipient_email' => strtolower(trim($user->email)),
                'recipient_name' => $user->name ?: ($user->username ?: 'Creator'),
                'status' => 'queued',
                'created_at' => $nowTimestamp,
                'updated_at' => $nowTimestamp,
            ];
        }

        foreach (array_chunk($recipientRows, 300) as $chunkRows) {
            CampaignRecipient::insert($chunkRows);
        }

        // 10. Sequential Batched Job Dispatching
        $recipientIds = CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', 'queued')
            ->pluck('id')
            ->toArray();

        $chunks = array_chunk($recipientIds, $batchSize);
        $currentDelay = 0;

        foreach ($chunks as $batchIds) {
            SendCampaignBatchJob::dispatch($campaign->id, $batchIds)
                ->delay(now()->addSeconds($currentDelay));

            $currentDelay += $batchDelaySeconds;
        }

        // 11. Record WeeklyDigestHistory
        WeeklyDigestHistory::updateOrCreate(
            ['period_key' => $range['period_key']],
            [
                'campaign_id' => $campaign->id,
                'period_start' => $range['start'],
                'period_end' => $range['end'],
                'photoshoots_count' => $totalPhotoshoots,
                'photoshoot_ids' => $photoshoots->pluck('id')->toArray(),
                'recipients_count' => $totalRecipients,
                'status' => 'dispatched',
            ]
        );

        Log::info("Weekly Photoshoot Digest dispatched.", [
            'campaign_id' => $campaign->id,
            'period_key' => $range['period_key'],
            'photoshoots' => $totalPhotoshoots,
            'recipients' => $totalRecipients,
            'batches' => $totalBatches,
            'total_delay_seconds' => $currentDelay,
        ]);

        return [
            'success' => true,
            'status' => 'dispatched',
            'campaign_id' => $campaign->id,
            'period_key' => $range['period_key'],
            'photoshoots_count' => $totalPhotoshoots,
            'recipients_count' => $totalRecipients,
            'batches_count' => $totalBatches,
            'duration_seconds' => $currentDelay,
            'message' => "Successfully launched Weekly Photoshoot Digest campaign #{$campaign->id} ({$totalPhotoshoots} photoshoots) to {$totalRecipients} subscribers in {$totalBatches} controlled batches.",
        ];
    }
}
