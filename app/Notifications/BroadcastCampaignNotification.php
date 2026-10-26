<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\EmailSuppression;
use App\Services\EmailTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BroadcastCampaignNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recipient;
    public $campaign;
    public $recipientRecord;
    public $tries = 2;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $recipient, EmailCampaign $campaign, $recipientRecord = null)
    {
        $this->recipient = $recipient;
        $this->campaign = $campaign;
        $this->recipientRecord = $recipientRecord;
        $this->queue = 'emails-bulk';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // 1. Emergency Kill-Switch Guard
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            $this->markFailed('Bulk email emergency kill-switch is engaged');
            return [];
        }

        // 2. Suppression Guard
        $email = is_object($notifiable) && isset($notifiable->email)
            ? $notifiable->email
            : ($this->recipient->email ?? null);

        if (EmailSuppression::isSuppressed($email)) {
            $this->markFailed('Recipient is in the suppression list (previous bounce or complaint)');
            return [];
        }

        return ['mail'];
    }

    /**
     * Helper to mark recipient as failed and advance progress.
     */
    protected function markFailed(string $reason): void
    {
        if ($this->recipientRecord) {
            $record = is_object($this->recipientRecord)
                ? $this->recipientRecord
                : CampaignRecipient::find($this->recipientRecord);

            if ($record && $record->status === 'queued') {
                $record->update([
                    'status' => 'failed',
                    'error_message' => $reason,
                ]);

                $this->campaign->increment('processed_count');
                $this->campaign->increment('failed_count');

                if ($this->campaign->processed_count >= $this->campaign->recipients_count) {
                    $this->campaign->update([
                        'status' => 'sent',
                        'completed_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $recipientName = $this->recipient->name ?: 'Creator';
        $token = $this->recipient->unsubscribe_token ?? base64_encode($this->recipient->email);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $vaultUrl = url('prompts/free');

        // Personalize tags inside the campaign content body
        $bodyMessage = str_replace(
            ['{name}', '{email}', '{unsubscribe_url}', '{vault_url}', '{vaultUrl}'],
            [htmlspecialchars($recipientName), htmlspecialchars($this->recipient->email), $unsubscribeUrl, $vaultUrl, $vaultUrl],
            $this->campaign->content
        );

        $settings = AdminSettings::first();
        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $senderEmail = $settings->email_sender_address_updates ?? 'hello@updates.imaginebuddy.com';
        $senderName = $settings->email_sender_name_updates ?? (($settings->title ?? 'Imagine Buddy') . ' Updates');
        $replyTo = $settings->email_reply_to_updates ?? 'support@imaginebuddy.com';
        $footerText = $settings->email_updates_footer_text;

        $html = view('emails.layouts.master-updates', [
            'settings' => $settings,
            'subject' => $this->campaign->subject,
            'bodyContent' => $bodyMessage,
            'previewText' => $this->campaign->preview_text,
            'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
            'footerText' => $footerText,
            'unsubscribeUrl' => $unsubscribeUrl,
        ])->render();

        $record = null;
        if ($this->recipientRecord) {
            $record = is_object($this->recipientRecord)
                ? $this->recipientRecord
                : CampaignRecipient::find($this->recipientRecord);

            if ($record) {
                $record->update(['status' => 'sending']);
                $html = EmailTrackingService::instrumentHtml($html, $record, $this->campaign);
            }
        }

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($senderEmail, $senderName)
            ->subject($this->campaign->subject)
            ->view('emails.raw', ['content' => $html]);

        if (!empty($replyTo)) {
            $mailMessage->replyTo($replyTo);
        }

        $campaignId = $this->campaign->id;
        $recipientRecordId = $record ? $record->id : null;

        $mailMessage->withSymfonyMessage(function ($message) use ($unsubscribeUrl, $campaignId, $recipientRecordId) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');

            if ($campaignId) {
                $message->getHeaders()->addTextHeader('X-Campaign-ID', (string) $campaignId);
                $message->getHeaders()->addTextHeader('X-SES-MESSAGE-TAGS', 'CampaignId=' . $campaignId);
            }

            if ($recipientRecordId) {
                $message->getHeaders()->addTextHeader('X-Recipient-ID', (string) $recipientRecordId);
            }
        });

        return $mailMessage;
    }

    /**
     * Handle queue execution failure.
     */
    public function failed(\Throwable $exception)
    {
        $this->markFailed('Queue exception: ' . substr($exception->getMessage(), 0, 500));
    }
}
