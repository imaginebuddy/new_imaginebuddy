<?php

namespace App\Console\Commands;

use App\Models\EmailSuppression;
use App\Models\Subscriptions;
use App\Notifications\SubscriptionExpiringAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendQuotaAlerts extends Command
{
    protected $signature = 'email:send-quota-alerts {--dry-run : Simulate sending without dispatching jobs}';
    protected $description = 'Send 3-day subscription expiration/renewal alerts to active subscribers';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $suppressed = EmailSuppression::pluck('email')->map(fn($e) => strtolower(trim($e)))->flip();

        $startWindow = now()->addDays(2);
        $endWindow = now()->addDays(3)->endOfDay();

        $subscriptions = Subscriptions::with('user')
            ->whereBetween('ends_at', [$startWindow, $endWindow])
            ->where(function ($q) {
                $q->whereNull('last_expiration_alert_sent_at')
                  ->orWhere('last_expiration_alert_sent_at', '<', now()->subDays(20));
            })
            ->get();

        $eligible = collect();

        foreach ($subscriptions as $sub) {
            $user = $sub->user;
            if (!$user || empty($user->email)) {
                continue;
            }

            $email = strtolower(trim($user->email));
            if (!isset($suppressed[$email])) {
                $eligible->push($sub);
            }
        }

        $total = $eligible->count();
        $this->info("Found {$total} subscriptions expiring in ~3 days.");

        if ($total === 0) {
            return 0;
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$total} expiration alerts would be dispatched.");
            return 0;
        }

        $dispatched = 0;
        foreach ($eligible as $subscription) {
            $subscription->user->notify(new SubscriptionExpiringAlertNotification($subscription));
            $subscription->update(['last_expiration_alert_sent_at' => now()]);
            $dispatched++;
        }

        $this->info("Successfully dispatched {$dispatched} subscription expiration alerts.");
        Log::info("Subscription 3-day expiration alert dispatched to {$dispatched} users.");

        return 0;
    }
}
