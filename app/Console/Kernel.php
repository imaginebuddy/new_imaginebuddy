<?php

namespace App\Console;

use App\Jobs\RebillWallet;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --queue=emails-urgent,default,emails-bulk --stop-when-empty --tries=3 --timeout=120')
            ->cron('* * * * *')
            ->withoutOverlapping();

        $schedule->job(new RebillWallet)->hourly();
        $schedule->command('subscriptions:check-expirations')->hourly();
        $schedule->command('subscriptions:refill-yearly')->hourly();
        $schedule->command('analytics:rollup')->hourly();

        // Automated Lifecycle Email Campaigns
        $schedule->command('email:send-weekly-drop')
            ->weeklyOn(2, '10:00')
            ->withoutOverlapping();

        $schedule->command('email:send-reengagement --days=30')
            ->monthlyOn(1, '11:00')
            ->withoutOverlapping();

        // Lifecycle Automations (Item 10)
        $schedule->command('email:send-welcome-drip')
            ->dailyAt('10:30')
            ->withoutOverlapping();

        $schedule->command('email:send-quota-alerts')
            ->dailyAt('09:00')
            ->withoutOverlapping();

        $schedule->command('email:send-abandoned-checkout')
            ->hourly()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
