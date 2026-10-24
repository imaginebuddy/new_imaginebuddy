<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterDoubleOptIn extends Notification implements ShouldQueue
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
        $confirmUrl = route('newsletter.confirm', ['token' => $this->subscriber->verification_token]);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->subscriber->verification_token]);

        $rendered = \App\Services\EmailTemplateService::render('newsletter-double-opt-in', [
            '{confirm_url}' => $confirmUrl,
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
                ->subject('Confirm your subscription to Imagine Buddy (and unlock 50+ AI Prompts)')
                ->view('emails.newsletter-double-opt-in', [
                    'subscriber' => $this->subscriber,
                    'confirmUrl' => $confirmUrl,
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
