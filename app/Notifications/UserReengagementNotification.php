<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserReengagementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recipient;
    public $prompts;
    public $tries = 2;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $recipient, $prompts)
    {
        $this->recipient = $recipient;
        $this->prompts = $prompts;
        $this->queue = 'emails-bulk';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
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

        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->recipient->unsubscribe_token]);

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($fromEmail, $fromName)
            ->subject('We miss you! Explore new AI photoshoot prompts on Imagine Buddy ✨')
            ->view('emails.user-reengagement', [
                'recipient' => $this->recipient,
                'prompts' => $this->prompts,
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
