<?php

namespace App\Console\Commands;

use App\Models\AdminSettings;
use App\Models\Analytics\AnalyticsEvent;
use App\Models\EmailSuppression;
use App\Models\Plans;
use App\Models\Subscriptions;
use App\Models\User;
use App\Notifications\AbandonedCheckoutRecoveryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendAbandonedCheckoutRecovery extends Command
{
    protected $signature = 'email:send-abandoned-checkout {--dry-run : Simulate sending without dispatching jobs}';
    protected $description = 'Recover abandoned checkouts for users who attempted payment 2-24 hours ago';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            $this->warn('Bulk email kill-switch is ENGAGED. Skipping Abandoned Checkout Recovery.');
            return 0;
        }

        $suppressed = EmailSuppression::pluck('email')->map(fn($e) => strtolower(trim($e)))->flip();

        $fromTime = now()->subHours(24);
        $toTime = now()->subHours(2);

        // Fetch payment_attempt events in the 2h - 24h window
        $attempts = AnalyticsEvent::where('event_name', 'payment_attempt')
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$fromTime, $toTime])
            ->latest('created_at')
            ->get();

        $candidates = collect();

        foreach ($attempts as $event) {
            $userId = $event->user_id;

            // Skip if already evaluated in this batch
            if ($candidates->has($userId)) {
                continue;
            }

            // Check if user has an active subscription
            $hasActiveSub = Subscriptions::where('user_id', $userId)
                ->where('ends_at', '>=', now())
                ->exists();

            if ($hasActiveSub) {
                continue;
            }

            // Check if recovery email was already sent in the last 30 days
            $alreadySent = DB::table('abandoned_checkout_logs')
                ->where('user_id', $userId)
                ->where('sent_at', '>=', now()->subDays(30))
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $user = User::find($userId);
            if (!$user || empty($user->email) || $user->status !== 'active') {
                continue;
            }

            $email = strtolower(trim($user->email));
            if (isset($suppressed[$email])) {
                continue;
            }

            $planId = $event->metadata['plan_id'] ?? null;
            $plan = $planId ? Plans::where('plan_id', $planId)->first() : null;

            $candidates->put($userId, [
                'user' => $user,
                'plan' => $plan,
                'plan_id' => $planId,
            ]);
        }

        $total = $candidates->count();
        $this->info("Found {$total} abandoned checkouts eligible for recovery.");

        if ($total === 0) {
            return 0;
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$total} abandoned checkout recovery emails would be sent.");
            return 0;
        }

        $dispatched = 0;
        foreach ($candidates as $item) {
            $user = $item['user'];
            $plan = $item['plan'];

            $user->notify(new AbandonedCheckoutRecoveryNotification($plan));

            DB::table('abandoned_checkout_logs')->insert([
                'user_id' => $user->id,
                'email' => $user->email,
                'plan_id' => $item['plan_id'],
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $dispatched++;
        }

        $this->info("Successfully dispatched {$dispatched} abandoned checkout recovery emails.");
        Log::info("Abandoned checkout recovery dispatched to {$dispatched} users.");

        return 0;
    }
}
