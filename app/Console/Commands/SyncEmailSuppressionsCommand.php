<?php

namespace App\Console\Commands;

use App\Services\EmailSuppressionSyncService;
use Illuminate\Console\Command;

class SyncEmailSuppressionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sync-suppressions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily synchronization of email bounces, AWS SES suppression list, and disposable email audit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting unified email deliverability synchronization...');

        $result = EmailSuppressionSyncService::run('cron');

        if ($result['success']) {
            $this->info("Synchronization completed in {$result['duration_seconds']}s.");
            $this->line("- Bounces reconciled: {$result['bounces_synced']}");
            $this->line("- Disposables detected: {$result['disposables_detected']}");
            $this->line("- Newly suppressed: {$result['newly_suppressed']}");

            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $err) {
                    $this->warn("Warning: {$err}");
                }
            }

            return Command::SUCCESS;
        }

        $this->error("Synchronization failed: {$result['error']}");
        return Command::FAILURE;
    }
}
