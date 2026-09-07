<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscriptions;
use App\Models\Plans;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RefillYearlySubscriptionQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:refill-yearly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refills the monthly download quota for active yearly subscribers whose monthly anniversary has arrived';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking yearly subscriptions for monthly quota refill...');

        $yearlySubscriptions = Subscriptions::where('interval', 'year')
            ->where('ends_at', '>', now())
            ->where('cancelled', 'no')
            ->where('stripe_status', 'active')
            ->get();

        $refilledCount = 0;

        foreach ($yearlySubscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user) {
                continue;
            }

            $plan = $subscription->plan ?: Plans::wherePlanId($subscription->stripe_price)->first();
            if (!$plan) {
                continue;
            }

            $lastRefill = Carbon::parse($subscription->last_refilled_at ?: $subscription->created_at);
            $nextRefillDue = $lastRefill->copy()->addMonth();

            // Check if 1 month has elapsed since last refill
            if (now()->greaterThanOrEqualTo($nextRefillDue)) {
                $monthlyQuota = $plan->downloads_per_month ?: 3000;

                if ($plan->unused_downloads_rollover) {
                    $user->increment('downloads', $monthlyQuota);
                } else {
                    $user->update(['downloads' => $monthlyQuota]);
                }

                // Advance last_refilled_at to the refill due date to keep subsequent months aligned
                $subscription->update([
                    'last_refilled_at' => $nextRefillDue
                ]);

                $this->line("Refilled {$monthlyQuota} downloads for user #{$user->id} ({$user->username}) on yearly subscription #{$subscription->id}.");
                Log::info("Refilled {$monthlyQuota} downloads for user #{$user->id} on yearly subscription #{$subscription->id}.");

                $refilledCount++;
            }
        }

        $this->info("Completed. Refilled quotas for {$refilledCount} yearly subscription(s).");
        return Command::SUCCESS;
    }
}
