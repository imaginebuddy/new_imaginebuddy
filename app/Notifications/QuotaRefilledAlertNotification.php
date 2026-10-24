<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotaRefilledAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $quotaAmount;
    public $tries = 2;

    public function __construct(int $quotaAmount)
    {
        $this->quotaAmount = $quotaAmount;
        $this->queue = 'emails-bulk';
    }

    public function via($notifiable)
    {
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return [];
        }

        $email = $notifiable->email ?? null;
        if (EmailSuppression::isSuppressed($email)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fromEmail = config('mail.mailers.ses-marketing.from.address', 'hello@updates.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-marketing.from.name', config('app.name', 'Imagine Buddy'));

        $token = $notifiable->token ?? base64_encode($notifiable->email);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $vaultUrl = url('prompts/free');

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($fromEmail, $fromName)
            ->subject('Your monthly download quota has been refilled! 🚀')
            ->view('emails.quota-refilled-alert', [
                'user' => $notifiable,
                'quotaAmount' => $this->quotaAmount,
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
