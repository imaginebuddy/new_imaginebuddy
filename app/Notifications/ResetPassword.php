<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->queue = 'emails-urgent';
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        if (EmailSuppression::isSuppressed($notifiable->email ?? null, 'auth')) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $name = $notifiable->name ?? 'User';
        $resetUrl = url('password/reset', $this->token);

        $rendered = \App\Services\EmailTemplateService::render('password-reset', [
            '{name}' => $name,
            '{action_url}' => $resetUrl,
            '{expire_minutes}' => config('auth.passwords.users.expire', 60),
            '{ip_address}' => request()->ip() ?? 'N/A',
        ], function () {
            $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
            $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy'));
            return [
                'used_custom' => false,
                'subject' => trans('emails.password_reset'),
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
            ->view('emails.password_reset', ['token' => $this->token]);
    }
}
