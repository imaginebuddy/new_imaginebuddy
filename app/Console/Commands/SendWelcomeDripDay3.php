<?php

namespace App\Console\Commands;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use App\Notifications\WelcomeDripDay3Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendWelcomeDripDay3 extends Command
{
    protected $signature = 'email:send-welcome-drip {--dry-run : Simulate sending without dispatching jobs}';
    protected $description = 'Send Day 3 educational onboarding drip to new users and confirmed subscribers';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            $this->warn('Bulk email kill-switch is ENGAGED. Skipping Welcome Drip.');
            return 0;
        }

        $suppressed = EmailSuppression::pluck('email')->map(fn($e) => strtolower(trim($e)))->flip();

        $threeDaysAgo = now()->subDays(3);
        $sevenDaysAgo = now()->subDays(7);

        // 1. Fetch eligible registered Google users
        $users = User::where('status', 'active')
            ->where('marketing_opt_in', 1)
            ->where('oauth_provider', 'google')
            ->whereNull('marketing_unsubscribed_at')
            ->whereNull('welcome_drip_sent_at')
            ->whereBetween('date', [$sevenDaysAgo, $threeDaysAgo])
            ->get();

        // 2. Fetch eligible confirmed newsletter subscribers
        $subscribers = NewsletterSubscribers::where('status', 'subscribed')
            ->whereNull('unsubscribed_at')
            ->whereNull('welcome_drip_sent_at')
            ->whereBetween('confirmed_at', [$sevenDaysAgo, $threeDaysAgo])
            ->get();

        $recipients = collect();

        foreach ($users as $u) {
            $email = strtolower(trim($u->email));
            if (!empty($email) && !isset($suppressed[$email])) {
                $recipients->put($email, (object) [
                    'email' => $email,
                    'name' => $u->name ?: $u->username,
                    'user_id' => $u->id,
                    'model' => $u,
                    'unsubscribe_token' => $u->token ?: base64_encode($email),
                ]);
            }
        }

        foreach ($subscribers as $s) {
            $email = strtolower(trim($s->email));
            if (!empty($email) && !isset($suppressed[$email])) {
                if (!$recipients->has($email)) {
                    $recipients->put($email, (object) [
                        'email' => $email,
                        'name' => 'Creator',
                        'user_id' => null,
                        'model' => $s,
                        'unsubscribe_token' => $s->verification_token,
                    ]);
                }
            }
        }

        $total = $recipients->count();
        $this->info("Found {$total} recipients eligible for Day 3 Welcome Drip.");

        if ($total === 0) {
            return 0;
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$total} Day 3 Welcome Drip emails would be dispatched.");
            return 0;
        }

        $dispatched = 0;
        foreach ($recipients as $recipient) {
            Notification::route('mail', $recipient->email)
                ->notify(new WelcomeDripDay3Notification($recipient));

            $recipient->model->update(['welcome_drip_sent_at' => now()]);
            $dispatched++;
        }

        $this->info("Successfully dispatched {$dispatched} Day 3 Welcome Drips.");
        Log::info("Welcome Day 3 Drip dispatched to {$dispatched} recipients.");

        return 0;
    }
}
