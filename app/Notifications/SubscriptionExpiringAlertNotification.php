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
        $name = $notifiable->name ?? 'User';
        $renewUrl = url('plans');
        $planName = $this->subscription->plan->name ?? ($this->subscription->type ?? 'Pro Plan');
        $expiresAt = $this->subscription->ends_at ? $this->subscription->ends_at->format('M d, Y') : 'soon';

        $rendered = \App\Services\EmailTemplateService::render('subscription-expiring', [
            '{name}' => $name,
            '{plan_name}' => $planName,
            '{expires_at}' => $expiresAt,
            '{action_url}' => $renewUrl,
        ], function () {
            $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
            $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy'));
            return [
                'used_custom' => false,
                'subject' => 'Important: Your Imagine Buddy subscription renews / expires in 3 days',
                'html' => null,
                'sender_name' => $fromName,
                'sender_email' => $fromEmail,
                'reply_to' => null,
            ];
        });

        $mailMessage = (new MailMessage)
            ->mailer('ses-transactional')
            ->from($rendered['sender_email'], $rendered['sender_name'])
            ->subject($rendered['subject']);

        if (!empty($rendered['reply_to'])) {
            $mailMessage->replyTo($rendered['reply_to']);
        }

        if (!empty($rendered['html'])) {
            return $mailMessage->view('emails.raw', ['content' => $rendered['html']]);
        }

        return $mailMessage
            ->view('emails.subscription-expiring-alert', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
                'renewUrl' => $renewUrl,
            ]);
    }
}
