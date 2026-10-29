<?php

namespace App\Console\Commands;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Notifications\NewsletterOptInReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendPendingOptInReminder extends Command
{
    protected $signature = 'email:send-pending-optin-reminder {--dry-run : Simulate sending without dispatching jobs}';
    protected $description = 'Send a 1-time 24-hour reminder to pending newsletter subscribers and expire stale opt-ins (>7 days)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            $this->warn('Bulk email kill-switch is ENGAGED. Skipping Pending Opt-In reminders.');
            return 0;
        }

        $this->info('Evaluating pending opt-in subscribers for 24-hour reminder...');

        // 1. Fetch suppressed emails into quick lookup
        $suppressed = EmailSuppression::pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->flip();

        // 2. Fetch pending subscribers created between 24 hours and 7 days ago who haven't received a reminder
        $eligible = NewsletterSubscribers::pendingReminder()
            ->whereNotNull('verification_token')
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($eligible as $subscriber) {
            $email = strtolower(trim($subscriber->email));

            if (empty($email) || isset($suppressed[$email])) {
                $skippedCount++;
                continue;
            }

            if ($dryRun) {
                $this->line("[DRY-RUN] Would send reminder to: {$email} (Created: {$subscriber->created_at})");
                $sentCount++;
                continue;
            }

            try {
                Notification::route('mail', $email)
                    ->notify(new NewsletterOptInReminder($subscriber));

                $subscriber->update([
                    'reminder_sent_at' => now(),
                ]);

                $sentCount++;
                $this->line("Dispatched reminder to: {$email}");
            } catch (\Throwable $e) {
                Log::error("Failed sending pending opt-in reminder to {$email}: " . $e->getMessage());
                $this->error("Failed to send reminder to {$email}: " . $e->getMessage());
            }
        }

        $this->info("Reminder Dispatches: {$sentCount} queued, {$skippedCount} skipped (suppressed).");

        // 3. Stale lead cleanup: expire pending opt-ins older than 7 days
        $expiredQuery = NewsletterSubscribers::expiredPending();
        $expiredCount = $expiredQuery->count();

        if ($expiredCount > 0) {
            if ($dryRun) {
                $this->line("[DRY-RUN] Would mark {$expiredCount} stale subscribers (>7 days) as expired.");
            } else {
                $expiredQuery->update([
                    'status' => 'expired',
                ]);
                $this->info("Expired {$expiredCount} unconfirmed subscribers older than 7 days.");
            }
        }

        Log::info("Pending Opt-In Reminder Job Completed.", [
            'sent' => $sentCount,
            'skipped_suppressed' => $skippedCount,
            'expired' => $expiredCount,
            'dry_run' => $dryRun,
        ]);

        return 0;
    }
}
