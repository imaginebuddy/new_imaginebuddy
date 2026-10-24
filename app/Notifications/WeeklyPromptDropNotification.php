<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyPromptDropNotification extends Notification implements ShouldQueue
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
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->recipient->unsubscribe_token]);
        $vaultUrl = url('prompts/free');

        $rendered = \App\Services\EmailTemplateService::render('weekly-prompt-drop', [
            '{name}' => $this->recipient->name ?: 'Creator',
            '{vault_url}' => $vaultUrl,
            '{current_date}' => now()->format('M d, Y'),
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
                ->subject('This Week\'s Top Curated AI Prompts & Photoshoot Recipes 📸')
                ->view('emails.weekly-prompt-drop', [
                    'recipient' => $this->recipient,
                    'prompts' => $this->prompts,
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
