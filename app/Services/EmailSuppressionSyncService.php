<?php

namespace App\Services;

use App\Models\CampaignRecipient;
use App\Models\EmailSuppression;
use App\Models\EmailSyncLog;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use Aws\SesV2\SesV2Client;
use Illuminate\Support\Facades\Log;

class EmailSuppressionSyncService
{
    /**
     * Run the complete unified email deliverability synchronization.
     *
     * @param string $triggeredBy 'cron' or 'admin'
     * @param int|null $adminId ID of admin if triggered manually
     * @return array
     */
    public static function run(string $triggeredBy = 'cron', ?int $adminId = null): array
    {
        $startTime = microtime(true);
        $errors = [];
        $bouncesSynced = 0;
        $newlySuppressed = 0;
        $sesApiSynced = 0;

        // Create log record
        $syncLog = EmailSyncLog::create([
            'triggered_by' => $triggeredBy,
            'admin_id' => $adminId,
            'status' => 'running',
        ]);

        try {
            // 1. Reconcile internal campaign bounce records
            $internalBounces = CampaignRecipient::whereIn('status', ['hard_bounced', 'failed'])
                ->select('recipient_email', 'status', 'bounce_type', 'created_at')
                ->get();

            foreach ($internalBounces as $b) {
                $email = strtolower(trim($b->recipient_email));
                if (empty($email)) {
                    continue;
                }

                $alreadySuppressed = EmailSuppression::where('email', $email)->exists();

                EmailSuppression::suppress(
                    $email,
                    'hard_bounce',
                    'campaign_reconciliation',
                    ['bounce_type' => $b->bounce_type, 'recorded_at' => $b->created_at],
                    'all'
                );

                User::where('email', $email)->update([
                    'marketing_opt_in' => 0,
                    'marketing_unsubscribed_at' => now(),
                ]);

                NewsletterSubscribers::where('email', $email)->update([
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => now(),
                ]);

                $bouncesSynced++;
                if (!$alreadySuppressed) {
                    $newlySuppressed++;
                }
            }

            // 2. Reconcile soft bounces (check if any soft bounced 3+ times)
            $softBounces = CampaignRecipient::where('status', 'soft_bounced')
                ->selectRaw('recipient_email, count(*) as count')
                ->groupBy('recipient_email')
                ->having('count', '>=', 3)
                ->get();

            foreach ($softBounces as $sb) {
                $email = strtolower(trim($sb->recipient_email));
                $alreadySuppressed = EmailSuppression::where('email', $email)->exists();

                EmailSuppression::suppress(
                    $email,
                    'hard_bounce',
                    'repeated_soft_bounce_threshold',
                    ['soft_bounce_count' => $sb->count],
                    'all'
                );

                $bouncesSynced++;
                if (!$alreadySuppressed) {
                    $newlySuppressed++;
                }
            }

            // 3. Reconcile with Amazon SES v2 Account-Level Suppression List
            if (config('services.ses.key') && config('services.ses.secret')) {
                try {
                    $sesClient = new SesV2Client([
                        'version' => 'latest',
                        'region' => config('services.ses.region', 'ap-south-1'),
                        'credentials' => [
                            'key' => config('services.ses.key'),
                            'secret' => config('services.ses.secret'),
                        ],
                    ]);

                    $nextToken = null;
                    do {
                        $params = ['PageSize' => 100];
                        if ($nextToken) {
                            $params['NextToken'] = $nextToken;
                        }

                        $result = $sesClient->listSuppressedDestinations($params);
                        $destinations = $result->get('SuppressedDestinationSummaries') ?: [];

                        foreach ($destinations as $dest) {
                            $email = strtolower(trim($dest['EmailAddress'] ?? ''));
                            if (empty($email)) {
                                continue;
                            }

                            $reason = strtolower($dest['Reason'] ?? 'hard_bounce');
                            $reasonMapped = match ($reason) {
                                'complaint' => 'complaint',
                                default => 'hard_bounce',
                            };

                            $alreadySuppressed = EmailSuppression::where('email', $email)->exists();

                            EmailSuppression::suppress(
                                $email,
                                $reasonMapped,
                                'aws_ses_reconciliation',
                                [
                                    'ses_reason' => $dest['Reason'] ?? null,
                                    'last_update_time' => isset($dest['LastUpdateTime']) ? (string) $dest['LastUpdateTime'] : null,
                                ],
                                'all'
                            );

                            $sesApiSynced++;
                            if (!$alreadySuppressed) {
                                $newlySuppressed++;
                            }
                        }

                        $nextToken = $result->get('NextToken');
                    } while ($nextToken);

                } catch (\Throwable $sesEx) {
                    $errors[] = 'AWS SES v2 ListSuppressedDestinations warning: ' . $sesEx->getMessage();
                    Log::warning('AWS SES suppression list sync warning: ' . $sesEx->getMessage());
                }
            }

            // 4. Run Disposable Email Audit
            $disposableAudit = DisposableEmailService::auditExistingUsers();
            $disposablesDetected = $disposableAudit['total_flagged'] ?? 0;
            $newlySuppressed += $disposablesDetected;

            $duration = round(microtime(true) - $startTime, 2);

            // Update log record
            $syncLog->update([
                'status' => 'completed',
                'bounces_synced' => $bouncesSynced + $sesApiSynced,
                'disposables_detected' => $disposablesDetected,
                'newly_suppressed' => $newlySuppressed,
                'duration_seconds' => $duration,
                'error_details' => !empty($errors) ? implode('; ', $errors) : null,
            ]);

            return [
                'success' => true,
                'bounces_synced' => $bouncesSynced + $sesApiSynced,
                'ses_api_synced' => $sesApiSynced,
                'disposables_detected' => $disposablesDetected,
                'newly_suppressed' => $newlySuppressed,
                'duration_seconds' => $duration,
                'errors' => $errors,
                'completed_at' => now()->format('M d, Y H:i:s'),
            ];

        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);

            $syncLog->update([
                'status' => 'failed',
                'error_details' => $e->getMessage(),
                'duration_seconds' => $duration,
            ]);

            Log::error('Email suppression sync failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'duration_seconds' => $duration,
            ];
        }
    }
}
