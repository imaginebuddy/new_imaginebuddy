<?php

namespace App\Notifications;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 2;

    public function __construct()
    {
        $this->queue = 'emails-urgent';
    }

    public function via($notifiable)
    {
        $email = $notifiable->email ?? null;
        if (EmailSuppression::isSuppressed($email, 'auth')) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $name = !empty($notifiable->name) ? $notifiable->name : (!empty($notifiable->username) ? $notifiable->username : 'Creator');
        $settings = AdminSettings::first();
        $dailyLimit = isset($settings->daily_limit_prompts) && $settings->daily_limit_prompts == 0 ? 'Unlimited' : ($settings->daily_limit_prompts ?? 5);
        $vaultUrl = url('photoshoots');
        $fromEmail = config('mail.mailers.ses-transactional.from.address', 'noreply@auth.imaginebuddy.com');
        $fromName = config('mail.mailers.ses-transactional.from.name', config('app.name', 'Imagine Buddy'));

        return (new MailMessage)
            ->mailer('ses-transactional')
            ->from($fromEmail, $fromName)
            ->subject('Welcome to ImagineBuddy! Your ' . $dailyLimit . ' daily prompt copies are active 🚀')
            ->greeting('Welcome, ' . $name . '!')
            ->line('Your free account is active. You have ' . $dailyLimit . ' commercial prompt copies available every single day.')
            ->line('Your prompt allowance resets automatically every day at midnight UTC.')
            ->action('Explore Commercial Photoshoot Prompts', $vaultUrl)
            ->line('Paste prompts directly into Midjourney, ChatGPT, or your favorite AI generator for studio-grade product visuals.')
            ->line('Thank you for joining ImagineBuddy!');
    }
}
