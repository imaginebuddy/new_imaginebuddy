<?php

namespace App\Console\Commands;

use App\Models\Images;
use App\Notifications\UserReengagementNotification;
use App\Services\AudienceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendReengagementCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-reengagement {--dry-run : Simulate sending without dispatching jobs} {--days=30 : Days of user inactivity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send re-engagement campaigns to inactive verified Google users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $days = (int) $this->option('days') ?: 30;

        $this->info("Starting Re-engagement Campaign dispatch for users inactive for {$days}+ days...");
        if ($dryRun) {
            $this->warn('DRY RUN MODE ENABLED: No emails will actually be queued or sent.');
        }

        // 1. Fetch audience
        $audience = AudienceService::getReengagementAudience($days);
        $total = $audience->count();

        $this->info("Found {$total} eligible inactive users.");

        if ($total === 0) {
            $this->comment('No inactive recipients matching criteria. Exiting.');
            return 0;
        }

        // 2. Fetch featured prompts
        $prompts = Images::where('status', 'active')
            ->whereNotNull('prompt')
            ->orderBy('date', 'desc')
            ->take(3)
            ->get(['id', 'title', 'prompt', 'description', 'thumbnail']);

        // 3. Queue emails
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $dispatched = 0;
        foreach ($audience->chunk(50) as $chunk) {
            foreach ($chunk as $recipient) {
                if (!$dryRun) {
                    Notification::route('mail', $recipient->email)
                        ->notify(new UserReengagementNotification($recipient, $prompts));
                }
                $dispatched++;
                $bar->advance();
            }

            usleep(100000);
        }

        $bar->finish();
        $this->newLine();

        $modeText = $dryRun ? 'simulated' : 'queued for delivery';
        $this->info("Successfully {$modeText} {$dispatched} re-engagement emails.");

        Log::info("Re-engagement campaign {$modeText}.", [
            'recipients_count' => $dispatched,
            'dry_run' => $dryRun,
        ]);

        return 0;
    }
}
