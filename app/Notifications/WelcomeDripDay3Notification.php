<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeDripDay3Notification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recipient;
    public $tries = 2;

    public function __construct(object $recipient)
    {
        $this->recipient = $recipient;
        $this->queue = 'emails-bulk';
    }

    public function via($notifiable)
    {
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return [];
        }

        $email = is_object($notifiable) && isset($notifiable->email)
            ? $notifiable->email
            : ($this->recipient->email ?? null);

        if (EmailSuppression::isSuppressed($email)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fromEmail = config('mail.mailers.ses-marketing.from.address', 'hello@updates.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-marketing.from.name', config('app.name', 'Imagine Buddy'));

        $token = $this->recipient->unsubscribe_token ?? base64_encode($this->recipient->email);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $vaultUrl = url('prompts/free');

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($fromEmail, $fromName)
            ->subject('3 studio lighting secrets for photorealistic AI mockups 💡')
            ->view('emails.welcome-drip-day3', [
                'recipient' => $this->recipient,
                'vaultUrl' => $vaultUrl,
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
