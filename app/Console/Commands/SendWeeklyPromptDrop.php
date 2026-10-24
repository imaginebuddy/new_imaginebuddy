<?php

namespace App\Console\Commands;

use App\Models\Images;
use App\Notifications\WeeklyPromptDropNotification;
use App\Services\AudienceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendWeeklyPromptDrop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-weekly-drop {--dry-run : Simulate sending without dispatching jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the weekly curated AI prompt drop to verified, opted-in subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Starting Weekly Prompt Drop dispatch...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE ENABLED: No emails will actually be queued or sent.');
        }

        // 1. Fetch deliverable recipients
        $audience = AudienceService::getDeliverableMarketingAudience();
        $total = $audience->count();

        $this->info("Found {$total} verified, deliverable recipients.");

        if ($total === 0) {
            $this->comment('No eligible recipients found. Exiting.');
            return 0;
        }

        // 2. Fetch latest curated prompts
        $prompts = Images::where('status', 'active')
            ->whereNotNull('prompt')
            ->orderBy('date', 'desc')
            ->take(3)
            ->get(['id', 'title', 'prompt', 'description', 'thumbnail']);

        $this->info("Fetched {$prompts->count()} featured prompts for inclusion.");

        // 3. Queue emails with rate-limiting chunking
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $dispatched = 0;
        foreach ($audience->chunk(50) as $chunk) {
            foreach ($chunk as $recipient) {
                if (!$dryRun) {
                    Notification::route('mail', $recipient->email)
                        ->notify(new WeeklyPromptDropNotification($recipient, $prompts));
                }
                $dispatched++;
                $bar->advance();
            }

            // Small 100ms throttle between chunks to protect system load
            usleep(100000);
        }

        $bar->finish();
        $this->newLine();

        $modeText = $dryRun ? 'simulated' : 'queued for delivery';
        $this->info("Successfully {$modeText} {$dispatched} weekly prompt drops.");

        Log::info("Weekly prompt drop campaign {$modeText}.", [
            'recipients_count' => $dispatched,
            'dry_run' => $dryRun,
        ]);

        return 0;
    }
}
