<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscriptions;
use App\Models\User;

class CheckSubscriptionExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired subscriptions, marks status, and resets expired download allowances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $expiredSubscriptions = Subscriptions::where('ends_at', '<', now())->get();
        $processedCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user) {
                continue;
            }

            // Check if user has any other active subscription
            $hasOtherActive = Subscriptions::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->where('ends_at', '>=', now())
                ->where('cancelled', 'no')
                ->exists();

            if (!$hasOtherActive) {
                // If user has no active subscription left, clear their premium subscription downloads
                if ($user->downloads > 0) {
                    $user->update(['downloads' => 0]);
                    $this->line("Reset downloads to 0 for user #{$user->id} ({$user->username})");
                }
            }

            // If Razorpay or Stripe subscription has not been marked expired, update status
            if ($subscription->stripe_status === 'active') {
                $subscription->update(['stripe_status' => 'expired']);
            }

            $processedCount++;
        }

        $this->info("Completed. Checked {$processedCount} expired subscriptions.");
        return Command::SUCCESS;
    }
}
