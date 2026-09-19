<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Analytics\AnalyticsSession;
use App\Models\Analytics\AnalyticsEvent;
use App\Models\Analytics\AnalyticsSearch;
use App\Models\Analytics\AnalyticsDailySummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsRollup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'analytics:rollup {--date= : The specific date (Y-m-d) to aggregate}';

    /**
     * The console command description.
     */
    protected $description = 'Aggregate raw analytics events and sessions into daily summaries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $specificDate = $this->option('date');
        $dates = [];

        if ($specificDate) {
            $dates[] = Carbon::parse($specificDate)->toDateString();
        } else {
            // Aggregate today and yesterday
            $dates[] = Carbon::yesterday()->toDateString();
            $dates[] = Carbon::today()->toDateString();
        }

        foreach ($dates as $date) {
            $this->aggregateDate($date);
        }

        // Clean up events older than 90 days
        $cutoff = Carbon::now()->subDays(90);
        $deleted = AnalyticsEvent::where('created_at', '<', $cutoff)->delete();
        if ($deleted > 0) {
            $this->info("Purged {$deleted} raw event rows older than 90 days.");
        }

        $this->info('Analytics aggregation complete.');
    }

    protected function aggregateDate(string $date)
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        // Query session metrics
        $sessionStats = AnalyticsSession::whereBetween('started_at', [$start, $end])
            ->selectRaw('
                COUNT(DISTINCT visitor_id) as total_visitors,
                COUNT(DISTINCT CASE WHEN is_bot = 0 THEN visitor_id END) as unique_visitors,
                COUNT(DISTINCT CASE WHEN is_bot = 0 AND is_new_visitor = 1 THEN visitor_id END) as new_visitors,
                COUNT(DISTINCT CASE WHEN is_bot = 0 AND is_new_visitor = 0 THEN visitor_id END) as returning_visitors,
                COUNT(DISTINCT CASE WHEN is_bot = 0 AND user_id IS NOT NULL THEN user_id END) as logged_in_users,
                COUNT(DISTINCT CASE WHEN is_bot = 0 AND user_id IS NULL THEN visitor_id END) as anonymous_users,
                COUNT(CASE WHEN is_bot = 0 THEN id END) as total_sessions,
                COALESCE(SUM(CASE WHEN is_bot = 0 THEN page_views_count ELSE 0 END), 0) as total_page_views
            ')
            ->first();

        // Query event metrics
        $eventStats = AnalyticsEvent::whereBetween('created_at', [$start, $end])
            ->selectRaw('
                COUNT(CASE WHEN event_name = "prompt_view" THEN 1 END) as prompt_views,
                COUNT(CASE WHEN event_name = "prompt_copy" THEN 1 END) as prompt_copies,
                COUNT(CASE WHEN event_name = "pricing_view" THEN 1 END) as pricing_views,
                COUNT(CASE WHEN event_name = "checkout_start" THEN 1 END) as checkout_starts,
                COUNT(CASE WHEN event_name = "payment_attempt" THEN 1 END) as payment_attempts,
                COUNT(CASE WHEN event_name = "payment_success" THEN 1 END) as payment_successes
            ')
            ->first();

        // Query search metrics
        $totalSearches = AnalyticsSearch::whereBetween('created_at', [$start, $end])->count();

        AnalyticsDailySummary::updateOrCreate(
            ['date' => $date],
            [
                'total_visitors' => $sessionStats->total_visitors ?? 0,
                'unique_visitors' => $sessionStats->unique_visitors ?? 0,
                'new_visitors' => $sessionStats->new_visitors ?? 0,
                'returning_visitors' => $sessionStats->returning_visitors ?? 0,
                'logged_in_users' => $sessionStats->logged_in_users ?? 0,
                'anonymous_users' => $sessionStats->anonymous_users ?? 0,
                'total_sessions' => $sessionStats->total_sessions ?? 0,
                'total_page_views' => $sessionStats->total_page_views ?? 0,
                'prompt_views' => $eventStats->prompt_views ?? 0,
                'prompt_copies' => $eventStats->prompt_copies ?? 0,
                'total_searches' => $totalSearches,
                'pricing_views' => $eventStats->pricing_views ?? 0,
                'checkout_starts' => $eventStats->checkout_starts ?? 0,
                'payment_attempts' => $eventStats->payment_attempts ?? 0,
                'payment_successes' => $eventStats->payment_successes ?? 0,
            ]
        );

        $this->line("Aggregated metrics for date: {$date}");
    }
}
