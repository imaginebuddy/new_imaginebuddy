<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\EmailCampaign;
use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BroadcastCampaignNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recipient;
    public $campaign;
    public $tries = 2;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $recipient, EmailCampaign $campaign)
    {
        $this->recipient = $recipient;
        $this->campaign = $campaign;
        $this->queue = 'emails-bulk';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // 1. Emergency Kill-Switch Guard
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return [];
        }

        // 2. Suppression Guard
        $email = is_object($notifiable) && isset($notifiable->email)
            ? $notifiable->email
            : ($this->recipient->email ?? null);

        if (EmailSuppression::isSuppressed($email)) {
            return [];
        }

        return ['mail'];
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

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($senderEmail, $senderName)
            ->subject($this->campaign->subject)
            ->view('emails.raw', ['content' => $html]);

        if (!empty($replyTo)) {
            $mailMessage->replyTo($replyTo);
        }

        $mailMessage->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return $mailMessage;
    }
}
