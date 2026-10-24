<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use App\Models\Subscriptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subscription;
    public $tries = 3;

    public function __construct(Subscriptions $subscription)
    {
        $this->subscription = $subscription;
        $this->queue = 'emails-urgent';
    }

    public function via($notifiable)
    {
        $email = $notifiable->email ?? null;
        if (EmailSuppression::isSuppressed($email)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy'));

        $renewUrl = url('plans');

        return (new MailMessage)
            ->mailer('ses-transactional')
            ->from($fromEmail, $fromName)
            ->subject('Important: Your Imagine Buddy subscription renews / expires in 3 days')
            ->view('emails.subscription-expiring-alert', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
                'renewUrl' => $renewUrl,
            ]);
    }
}
