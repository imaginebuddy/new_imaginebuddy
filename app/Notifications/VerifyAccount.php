<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyAccount extends Notification implements ShouldQueue
{
    use Queueable;

    public $confirmation_code;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $confirmation_code)
    {
        $this->confirmation_code = $confirmation_code;
        $this->queue = 'emails-urgent';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (EmailSuppression::isSuppressed($notifiable->email ?? null)) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $name = $notifiable->name ?? 'User';
        $verifyUrl = url('verify/account', $this->confirmation_code);

        $rendered = \App\Services\EmailTemplateService::render('verify-account', [
            '{name}' => $name,
            '{action_url}' => $verifyUrl,
            '{support_email}' => config('mail.from.address', 'support@imaginebuddy.com'),
        ], function () {
            $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
            $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy'));
            return [
                'used_custom' => false,
                'subject' => trans('users.title_email_verify'),
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
            ->view('emails.verify', ['confirmation_code' => $this->confirmation_code]);
    }
}
