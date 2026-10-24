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
        $fromEmail = config('mail.mailers.ses-marketing.from.address', 'hello@updates.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-marketing.from.name', config('app.name', 'Imagine Buddy'));

        $token = $this->recipient->unsubscribe_token ?? base64_encode($this->recipient->email);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $vaultUrl = url('prompts/free');

        // Personalize tags
        $content = str_replace(
            ['{name}', '{email}', '{unsubscribe_url}', '{vault_url}'],
            [
                htmlspecialchars($this->recipient->name ?: 'Creator'),
                htmlspecialchars($this->recipient->email),
                $unsubscribeUrl,
                $vaultUrl,
            ],
            $this->campaign->content
        );

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($fromEmail, $fromName)
            ->subject($this->campaign->subject)
            ->view('emails.broadcast-template', [
                'campaign' => $this->campaign,
                'renderedContent' => $content,
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);

        $mailMessage->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return $mailMessage;
    }
}
