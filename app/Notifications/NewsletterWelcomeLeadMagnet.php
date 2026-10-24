<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterWelcomeLeadMagnet extends Notification implements ShouldQueue
{
    use Queueable;

    public $subscriber;
    public $tries = 3;

    /**
     * Create a new notification instance.
     */
    public function __construct(NewsletterSubscribers $subscriber)
    {
        $this->subscriber = $subscriber;
        $this->queue = 'emails-urgent';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $email = is_object($notifiable) && isset($notifiable->email)
            ? $notifiable->email
            : ($this->subscriber->email ?? null);

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
        $vaultUrl = url('prompts/free');
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->subscriber->verification_token]);

        $rendered = \App\Services\EmailTemplateService::render('newsletter-welcome', [
            '{name}' => 'Creator',
            '{vault_url}' => $vaultUrl,
            '{unsubscribe_url}' => $unsubscribeUrl,
        ]);

        if ($rendered['used_custom']) {
            $mailMessage = (new MailMessage)
                ->mailer($rendered['mailer'] ?? 'ses-marketing')
                ->from($rendered['sender_email'], $rendered['sender_name'])
                ->subject($rendered['subject'])
                ->view('emails.raw', ['content' => $rendered['html']]);

            if (!empty($rendered['reply_to'])) {
                $mailMessage->replyTo($rendered['reply_to']);
            }
        } else {
            $fromEmail = config('mail.mailers.ses-marketing.from.address', 'hello@updates.imaginebuddy.com');
            $fromName = config('mail.mailers.ses-marketing.from.name', config('app.name', 'Imagine Buddy'));

            $mailMessage = (new MailMessage)
                ->mailer('ses-marketing')
                ->from($fromEmail, $fromName)
                ->subject('Welcome! Here is your 50+ AI Prompts Master Vault')
                ->view('emails.newsletter-welcome-lead-magnet', [
                    'subscriber' => $this->subscriber,
                    'vaultUrl' => $vaultUrl,
                    'unsubscribeUrl' => $unsubscribeUrl,
                ]);
        }

        $mailMessage->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return $mailMessage;
    }
}
