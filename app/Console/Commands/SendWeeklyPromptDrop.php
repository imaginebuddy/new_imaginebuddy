<?php

namespace App\Console\Commands;

use App\Services\WeeklyPhotoshootDigestService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWeeklyPromptDrop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-weekly-drop 
                            {--dry-run : Simulate sending without dispatching jobs}
                            {--force : Force execution even if already sent for this period}
                            {--batch-size=50 : Number of recipients per batch}
                            {--batch-delay=15 : Delay between batches in seconds}
                            {--date= : Reference date (YYYY-MM-DD) for backtesting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the Weekly Photoshoot Drop Digest featuring newly published sets in controlled batches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $batchSize = max(5, (int) $this->option('batch-size') ?: 50);
        $batchDelay = max(0, (int) $this->option('batch-delay') ?: 15);
        $dateStr = $this->option('date');
        $referenceDate = $dateStr ? Carbon::parse($dateStr) : null;

        $this->info('Starting Weekly Photoshoot Drop Digest workflow...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE ENABLED: No campaigns or batches will be dispatched.');
        }

        $result = WeeklyPhotoshootDigestService::run(
            dryRun: $dryRun,
            force: $force,
            batchSize: $batchSize,
            batchDelaySeconds: $batchDelay,
            referenceDate: $referenceDate
        );

        if (!$result['success']) {
            $this->warn($result['message'] ?? 'Workflow aborted.');
            return 1;
        }

        if ($dryRun) {
            $this->newLine();
            $this->info('--- DRY RUN SUMMARY ---');
            $this->line("Period: {$result['period_label']} ({$result['period_key']})");
            $this->line("Eligible Photoshoots: {$result['photoshoots_count']}");
            if (!empty($result['photoshoot_titles'])) {
                foreach ($result['photoshoot_titles'] as $idx => $title) {
                    $this->line("  " . ($idx + 1) . ". {$title}");
                }
            }
            $this->line("Audience Size: {$result['recipients_count']} subscribers");
            $this->line("Batches: {$result['total_batches']} (Batch size: {$result['batch_size']})");
            $this->line("Staggered Delay: {$result['batch_delay_seconds']}s between batches");
            $this->line("Estimated Dispatch Window: {$result['estimated_duration_seconds']}s");
            $this->newLine();
            return 0;
        }

        if (($result['status'] ?? '') === 'skipped_no_photoshoots') {
            $this->comment($result['message']);
            return 0;
        }

        $this->newLine();
        $this->info("SUCCESS: {$result['message']}");
        $this->line("Campaign ID: #{$result['campaign_id']}");
        $this->line("Monitor progress in Admin Panel at: " . url('panel/admin/email-marketing/campaigns'));
        $this->newLine();

        return 0;
    }
}
