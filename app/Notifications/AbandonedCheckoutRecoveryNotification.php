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
        $token = $notifiable->token ?? base64_encode($notifiable->email);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $checkoutUrl = url('plans');

        $rendered = \App\Services\EmailTemplateService::render('abandoned-checkout', [
            '{name}' => $notifiable->name ?? 'Creator',
            '{plan_name}' => $this->plan->name ?? 'Pro Creator',
            '{checkout_url}' => $checkoutUrl,
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
                ->subject('Did you leave something behind? Complete your Imagine Buddy upgrade')
                ->view('emails.abandoned-checkout-recovery', [
                    'user' => $notifiable,
                    'plan' => $this->plan,
                    'checkoutUrl' => $checkoutUrl,
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
