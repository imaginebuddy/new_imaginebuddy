<?php

namespace App\Console\Commands;

use App\Models\EmailSuppression;
use App\Models\User;
use Illuminate\Console\Command;

class OptInLegacyGoogleUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:opt-in-legacy-google-users {--dry-run : Preview without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely opt-in legacy Google OAuth users who are verified by Google';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $suppressed = EmailSuppression::pluck('email')->map(fn($e) => strtolower(trim($e)))->flip();

        $query = User::where('oauth_provider', 'google')
            ->where('status', 'active')
            ->where('marketing_opt_in', 0)
            ->whereNull('marketing_unsubscribed_at');

        $count = $query->count();

        $this->info("Found {$count} active Google OAuth users eligible for safe marketing opt-in.");

        if ($count === 0) {
            $this->comment('No eligible users found.');
            return 0;
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$count} users would be updated to marketing_opt_in = 1.");
            return 0;
        }

        $updated = 0;
        $query->chunkById(200, function ($users) use (&$updated, $suppressed) {
            foreach ($users as $user) {
                $email = strtolower(trim($user->email));
                if (!isset($suppressed[$email])) {
                    $user->update([
                        'marketing_opt_in' => 1,
                        'marketing_opt_in_at' => now(),
                    ]);
                    $updated++;
                }
            }
        });

        $this->info("Successfully opted in {$updated} verified Google OAuth users.");
        return 0;
    }
}
