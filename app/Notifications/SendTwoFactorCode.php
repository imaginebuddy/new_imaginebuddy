<?php

namespace App\Notifications;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTwoFactorCode extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

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
    public function __construct($data)
    {
        $this->data = $data;
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
        $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy Security'));

        return (new MailMessage)
            ->mailer('ses-transactional')
            ->from($fromEmail, $fromName)
            ->subject(trans('misc.two_step_authentication_code'))
            ->greeting(trans('emails.hello') . ' ' . $notifiable->name)
            ->line(trans('misc.your_code_is', ['code' => $this->data['code']]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
