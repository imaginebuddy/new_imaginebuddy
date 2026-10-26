<?php

namespace App\Jobs;

use App\Models\AdminSettings;
use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\EmailSuppression;
use App\Services\EmailTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;
    public array $recipientIds;
    public int $tries = 2;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(int $campaignId, array $recipientIds)
    {
        $this->campaignId = $campaignId;
        $this->recipientIds = $recipientIds;
        $this->queue = 'emails-bulk';
    }

    /**
     * Execute the batch sending job.
     */
    public function handle(): void
    {
        $campaign = EmailCampaign::find($this->campaignId);
        if (!$campaign || in_array($campaign->status, ['cancelled', 'draft'])) {
            return;
        }

        // Emergency Kill Switch Guard
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            CampaignRecipient::whereIn('id', $this->recipientIds)
                ->where('status', 'queued')
                ->update([
                    'status' => 'failed',
                    'error_message' => 'Bulk email emergency kill-switch is engaged',
                ]);
            return;
        }

        $recipients = CampaignRecipient::whereIn('id', $this->recipientIds)
            ->whereIn('status', ['queued', 'sending'])
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $settings = AdminSettings::first();
        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $senderEmail = $settings->email_sender_address_updates ?? 'hello@updates.imaginebuddy.com';
        $senderName = $settings->email_sender_name_updates ?? (($settings->title ?? 'Imagine Buddy') . ' Updates');
        $replyTo = $settings->email_reply_to_updates ?? 'support@imaginebuddy.com';
        $footerText = $settings->email_updates_footer_text;

        foreach ($recipients as $recipient) {
            // Suppression Guard
            if (EmailSuppression::isSuppressed($recipient->recipient_email)) {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => 'Suppressed (previous bounce or complaint)',
                ]);
                $campaign->increment('processed_count');
                $campaign->increment('failed_count');
                continue;
            }

            $recipientName = $recipient->recipient_name ?: 'Creator';
            $token = base64_encode($recipient->recipient_email);
            $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
            $vaultUrl = url('prompts/free');

            // Personalize tags
            $bodyMessage = str_replace(
                ['{name}', '{email}', '{unsubscribe_url}', '{vault_url}', '{vaultUrl}'],
                [htmlspecialchars($recipientName), htmlspecialchars($recipient->recipient_email), $unsubscribeUrl, $vaultUrl, $vaultUrl],
                $campaign->content
            );

            // Wrap inside master updates light layout
            $html = view('emails.layouts.master-updates', [
                'settings' => $settings,
                'subject' => $campaign->subject,
                'bodyContent' => $bodyMessage,
                'previewText' => $campaign->preview_text,
                'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
                'primaryColor' => $primaryColor,
                'buttonColor' => $buttonColor,
                'footerText' => $footerText,
                'unsubscribeUrl' => $unsubscribeUrl,
            ])->render();

            // Instrument with tracking pixel & click redirection
            $recipient->update(['status' => 'sending']);
            $html = EmailTrackingService::instrumentHtml($html, $recipient, $campaign);

            try {
                Mail::mailer('ses-marketing')->send('emails.raw', ['content' => $html], function ($message) use ($campaign, $recipient, $senderEmail, $senderName, $replyTo, $unsubscribeUrl) {
                    $message->from($senderEmail, $senderName)
                        ->to($recipient->recipient_email)
                        ->subject($campaign->subject);

                    if (!empty($replyTo)) {
                        $message->replyTo($replyTo);
                    }

                    $message->getHeaders()
                        ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                        ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click')
                        ->addTextHeader('X-Campaign-ID', (string) $campaign->id)
                        ->addTextHeader('X-Recipient-ID', (string) $recipient->id)
                        ->addTextHeader('X-SES-MESSAGE-TAGS', 'CampaignId=' . $campaign->id);
                });

                // Mark sent
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $campaign->increment('processed_count');
                $campaign->increment('sent_count');

            } catch (\Throwable $e) {
                Log::error("Failed sending to {$recipient->recipient_email}: " . $e->getMessage());

                $recipient->update([
                    'status' => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 500),
                ]);

                $campaign->increment('processed_count');
                $campaign->increment('failed_count');
            }

            usleep(25000); // 25ms throttle between emails (40 emails/sec max to respect SES rate limits)
        }

        // Check if overall campaign finished
        $campaign->refresh();
        if ($campaign->processed_count >= $campaign->recipients_count) {
            $campaign->update([
                'status' => 'sent',
                'completed_at' => now(),
            ]);
            Log::info("Broadcast campaign '{$campaign->name}' completed delivery to all recipients.", [
                'campaign_id' => $campaign->id,
                'total_processed' => $campaign->processed_count,
            ]);
        }
    }
}
