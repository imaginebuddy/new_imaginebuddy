<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use App\Models\Plans;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCheckoutRecoveryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $plan;
    public $tries = 2;

    public function __construct(?Plans $plan = null)
    {
        $this->plan = $plan;
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
        $checkoutUrl = url('plans');

        $mailMessage = (new MailMessage)
            ->mailer('ses-marketing')
            ->from($fromEmail, $fromName)
            ->subject('Did you leave something behind? Complete your Imagine Buddy upgrade')
            ->view('emails.abandoned-checkout-recovery', [
                'user' => $notifiable,
                'plan' => $this->plan,
                'checkoutUrl' => $checkoutUrl,
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
