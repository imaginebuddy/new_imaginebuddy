<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analytics\AnalyticsSession;
use App\Models\Analytics\AnalyticsEvent;
use App\Models\Analytics\AnalyticsSearch;
use App\Models\Analytics\AnalyticsDailySummary;
use App\Models\User;
use App\Models\Images;
use App\Models\AdminSettings;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Helper;

class AnalyticsController extends Controller
{
    protected $settings;

    public function __construct(AdminSettings $settings)
    {
        $this->settings = $settings::first();
    }

    /**
     * Parse date range filters from request.
     */
    protected function parseDateRange(Request $request): array
    {
        $range = $request->get('range', '30d');
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Today (' . $start->format('M d, Y') . ')';
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $label = 'Yesterday (' . $start->format('M d, Y') . ')';
                break;
            case '7d':
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $label = 'Last 7 Days';
                break;
            case 'custom':
                $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
                $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
                $label = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
                break;
            case '30d':
            default:
                $range = '30d';
                $start = Carbon::now()->subDays(29)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $label = 'Last 30 Days';
                break;
        }

        return [$start, $end, $range, $label];
    }

    /**
     * Apply common bot and user filters to session queries.
     */
    protected function filterSessions($query, Request $request)
    {
        $botFilter = $request->get('bot_filter', 'exclude'); // exclude, only, all
        if ($botFilter === 'exclude') {
            $query->where('is_bot', 0);
        } elseif ($botFilter === 'only') {
            $query->where('is_bot', '>', 0);
        }

        $userType = $request->get('user_type', 'all'); // all, logged_in, anonymous
        if ($userType === 'logged_in') {
            $query->whereNotNull('user_id');
        } elseif ($userType === 'anonymous') {
            $query->whereNull('user_id');
        }

        return $query;
    }

    /**
     * Overview / Dashboard view.
     */
    public function overview(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        // Core Sessions KPI
        $sessionQuery = AnalyticsSession::whereBetween('started_at', [$start, $end]);
        $this->filterSessions($sessionQuery, $request);

        $sessionStats = (clone $sessionQuery)->selectRaw('
            COUNT(DISTINCT visitor_id) as unique_visitors,
            COUNT(DISTINCT CASE WHEN is_new_visitor = 1 THEN visitor_id END) as new_visitors,
            COUNT(DISTINCT CASE WHEN is_new_visitor = 0 THEN visitor_id END) as returning_visitors,
            COUNT(DISTINCT CASE WHEN user_id IS NOT NULL THEN user_id END) as logged_in_users,
            COUNT(DISTINCT CASE WHEN user_id IS NULL THEN visitor_id END) as anonymous_users,
            COUNT(id) as total_sessions,
            COALESCE(SUM(page_views_count), 0) as total_page_views,
            COALESCE(AVG(duration_seconds), 0) as avg_duration,
            COUNT(CASE WHEN is_engaged = 1 THEN 1 END) as engaged_sessions
        ')->first();

        // Event KPIs
        $eventQuery = AnalyticsEvent::whereBetween('created_at', [$start, $end]);
        if ($request->get('user_type') === 'logged_in') {
            $eventQuery->whereNotNull('user_id');
        } elseif ($request->get('user_type') === 'anonymous') {
            $eventQuery->whereNull('user_id');
        }

        $eventStats = (clone $eventQuery)->selectRaw('
            COUNT(CASE WHEN event_name = "prompt_view" THEN 1 END) as prompt_views,
            COUNT(CASE WHEN event_name = "prompt_copy" THEN 1 END) as prompt_copies,
            COUNT(CASE WHEN event_name = "prompt_copy_attempt" THEN 1 END) as prompt_copy_attempts,
            COUNT(CASE WHEN event_name = "pricing_view" THEN 1 END) as pricing_views,
            COUNT(CASE WHEN event_name = "checkout_start" THEN 1 END) as checkout_starts,
            COUNT(CASE WHEN event_name = "payment_attempt" THEN 1 END) as payment_attempts,
            COUNT(CASE WHEN event_name = "payment_success" THEN 1 END) as payment_successes
        ')->first();

        $totalSearches = AnalyticsSearch::whereBetween('created_at', [$start, $end])->count();

        // Timeline chart data (grouped by date)
        $timelineData = [];
        $daysCount = $start->diffInDays($end) + 1;
        $intervalDays = max(1, min(60, $daysCount));

        $chartDates = [];
        $chartVisitors = [];
        $chartPageviews = [];

        for ($i = 0; $i < $intervalDays; $i++) {
            $d = (clone $start)->addDays($i);
            if ($d > $end) break;
            $dStr = $d->toDateString();
            $dLabel = $d->format('M d');

            $dayStats = AnalyticsSession::whereDate('started_at', $dStr);
            $this->filterSessions($dayStats, $request);
            $dayRes = $dayStats->selectRaw('COUNT(DISTINCT visitor_id) as v, COALESCE(SUM(page_views_count), 0) as pv')->first();

            $chartDates[] = "'$dLabel'";
            $chartVisitors[] = (int) ($dayRes->v ?? 0);
            $chartPageviews[] = (int) ($dayRes->pv ?? 0);
        }

        // Top 5 Prompts
        $topPrompts = AnalyticsEvent::where('event_name', 'prompt_view')
            ->whereNotNull('image_id')
            ->whereBetween('created_at', [$start, $end])
            ->select('image_id', DB::raw('COUNT(id) as views_count'))
            ->groupBy('image_id')
            ->orderByDesc('views_count')
            ->take(5)
            ->with(['image' => function($q) {
                $q->select('id', 'title', 'thumbnail', 'slug', 'ai_model', 'item_for_sale');
            }])
            ->get();

        // Top 5 Searches
        $topSearches = AnalyticsSearch::whereBetween('created_at', [$start, $end])
            ->select('query', DB::raw('COUNT(id) as count'), DB::raw('AVG(results_count) as avg_results'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Traffic sources summary
        $trafficSources = (clone $sessionQuery)->select('referrer_domain', DB::raw('COUNT(id) as count'))
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Active live users count (distinct users/visitors)
        $thresholdLive = Carbon::now()->subMinutes(5);
        $liveUsersCount = AnalyticsSession::where('is_bot', 0)
            ->where('last_activity_at', '>=', $thresholdLive)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id')
            + AnalyticsSession::where('is_bot', 0)
            ->where('last_activity_at', '>=', $thresholdLive)
            ->whereNull('user_id')
            ->distinct('visitor_id')
            ->count('visitor_id');

        return view('admin.analytics.overview', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'sessionStats' => $sessionStats,
            'eventStats' => $eventStats,
            'totalSearches' => $totalSearches,
            'chartDates' => implode(',', $chartDates),
            'chartVisitors' => implode(',', $chartVisitors),
            'chartPageviews' => implode(',', $chartPageviews),
            'topPrompts' => $topPrompts,
            'topSearches' => $topSearches,
            'trafficSources' => $trafficSources,
            'liveUsersCount' => $liveUsersCount,
        ]);
    }

    /**
     * Live Active Users view.
     */
    public function live(Request $request)
    {
        $threshold = Carbon::now()->subMinutes(5);

        // Get latest session id for each distinct user or visitor
        $latestSessionIds = AnalyticsSession::where('is_bot', 0)
            ->where('last_activity_at', '>=', $threshold)
            ->selectRaw('MAX(id) as id')
            ->groupByRaw('CASE WHEN user_id IS NOT NULL THEN CONCAT("u_", user_id) ELSE CONCAT("v_", visitor_id) END')
            ->pluck('id');

        $liveSessions = AnalyticsSession::whereIn('id', $latestSessionIds)
            ->with('user:id,username,name,avatar')
            ->orderByDesc('last_activity_at')
            ->get();

        $activeRegistered = $liveSessions->whereNotNull('user_id')->count();
        $activeAnonymous = $liveSessions->whereNull('user_id')->count();
        $activeTotal = $activeRegistered + $activeAnonymous;

        return view('admin.analytics.live', [
            'settings' => $this->settings,
            'liveSessions' => $liveSessions,
            'activeTotal' => $activeTotal,
            'activeRegistered' => $activeRegistered,
            'activeAnonymous' => $activeAnonymous,
        ]);
    }

    /**
     * AJAX endpoint for live active users polled every 10s.
     */
    public function liveData(Request $request)
    {
        $threshold = Carbon::now()->subMinutes(5);

        $latestSessionIds = AnalyticsSession::where('is_bot', 0)
            ->where('last_activity_at', '>=', $threshold)
            ->selectRaw('MAX(id) as id')
            ->groupByRaw('CASE WHEN user_id IS NOT NULL THEN CONCAT("u_", user_id) ELSE CONCAT("v_", visitor_id) END')
            ->pluck('id');

        $sessions = AnalyticsSession::whereIn('id', $latestSessionIds)
            ->with('user:id,username,name,avatar')
            ->orderByDesc('last_activity_at')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'is_logged_in' => !empty($s->user_id),
                    'user_name' => $s->user ? ($s->user->name ?: $s->user->username) : 'Anonymous Visitor #' . substr($s->visitor_id, 0, 6),
                    'user_avatar' => $s->user ? \Illuminate\Support\Facades\Storage::url(config('path.avatar') . $s->user->avatar) : null,
                    'user_url' => $s->user ? url($s->user->username) : null,
                    'current_page' => $s->exit_page ?: '/',
                    'device_type' => ucfirst($s->device_type),
                    'browser' => $s->browser,
                    'country_code' => $s->country_code ?: 'UN',
                    'duration_formatted' => gmdate('H:i:s', $s->duration_seconds),
                    'last_seen_diff' => $s->last_activity_at ? $s->last_activity_at->diffForHumans() : 'Just now',
                ];
            });

        return response()->json([
            'total' => $sessions->count(),
            'registered' => $sessions->where('is_logged_in', true)->count(),
            'anonymous' => $sessions->where('is_logged_in', false)->count(),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Traffic & Visits view.
     */
    public function traffic(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        $query = AnalyticsSession::whereBetween('started_at', [$start, $end]);
        $this->filterSessions($query, $request);

        // Breakdowns
        $devices = (clone $query)->select('device_type', DB::raw('COUNT(id) as count'))
            ->groupBy('device_type')
            ->get();

        $browsers = (clone $query)->select('browser', DB::raw('COUNT(id) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        $operatingSystems = (clone $query)->select('os', DB::raw('COUNT(id) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        $referrers = (clone $query)->select('referrer_domain', DB::raw('COUNT(id) as count'))
            ->whereNotNull('referrer_domain')
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->paginate(10, ['*'], 'ref_page');

        $entryPages = (clone $query)->select('entry_page', DB::raw('COUNT(id) as count'))
            ->whereNotNull('entry_page')
            ->groupBy('entry_page')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        $countries = (clone $query)->select('country_code', DB::raw('COUNT(id) as count'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Detailed session logs
        $sessions = (clone $query)->with('user:id,username,name')
            ->orderByDesc('started_at')
            ->paginate(25);

        return view('admin.analytics.traffic', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'devices' => $devices,
            'browsers' => $browsers,
            'operatingSystems' => $operatingSystems,
            'referrers' => $referrers,
            'entryPages' => $entryPages,
            'countries' => $countries,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Search Analytics view.
     */
    public function searches(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        $query = AnalyticsSearch::whereBetween('created_at', [$start, $end]);

        if ($request->get('user_type') === 'logged_in') {
            $query->whereNotNull('user_id');
        } elseif ($request->get('user_type') === 'anonymous') {
            $query->whereNull('user_id');
        }

        if ($searchQuery = $request->get('q')) {
            $query->where('query', 'LIKE', "%{$searchQuery}%");
        }

        $totalSearches = (clone $query)->count();
        $uniqueQueries = (clone $query)->distinct('query')->count('query');
        $zeroResultSearches = (clone $query)->where('results_count', 0)->count();

        // Top searched queries
        $topQueries = (clone $query)->select('query', DB::raw('COUNT(id) as count'), DB::raw('AVG(results_count) as avg_results'), DB::raw('MAX(created_at) as last_searched'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->paginate(15);

        // Zero result queries
        $zeroResults = AnalyticsSearch::whereBetween('created_at', [$start, $end])
            ->where('results_count', 0)
            ->select('query', DB::raw('COUNT(id) as count'), DB::raw('MAX(created_at) as last_searched'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return view('admin.analytics.searches', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'totalSearches' => $totalSearches,
            'uniqueQueries' => $uniqueQueries,
            'zeroResultSearches' => $zeroResultSearches,
            'topQueries' => $topQueries,
            'zeroResults' => $zeroResults,
        ]);
    }

    /**
     * Prompt Analytics & Copy Activity view.
     */
    public function prompts(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        $eventQuery = AnalyticsEvent::whereBetween('created_at', [$start, $end]);

        $totalViews = (clone $eventQuery)->where('event_name', 'prompt_view')->count();
        $totalCopies = (clone $eventQuery)->where('event_name', 'prompt_copy')->count();
        $totalBlockedAttempts = (clone $eventQuery)->where('event_name', 'prompt_copy_attempt')->count();
        $uniqueCopiers = (clone $eventQuery)->where('event_name', 'prompt_copy')->distinct('visitor_id')->count('visitor_id');

        // Most viewed prompts
        $mostViewed = AnalyticsEvent::where('event_name', 'prompt_view')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('image_id')
            ->select('image_id', DB::raw('COUNT(id) as views_count'), DB::raw('COUNT(DISTINCT visitor_id) as unique_viewers'))
            ->groupBy('image_id')
            ->orderByDesc('views_count')
            ->with(['image' => function($q) {
                $q->select('id', 'title', 'thumbnail', 'slug', 'ai_model', 'item_for_sale', 'copies_count');
            }])
            ->paginate(15, ['*'], 'views_page');

        // Most copied prompts
        $mostCopied = AnalyticsEvent::where('event_name', 'prompt_copy')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('image_id')
            ->select('image_id', DB::raw('COUNT(id) as copies_count'), DB::raw('COUNT(DISTINCT visitor_id) as unique_copiers'))
            ->groupBy('image_id')
            ->orderByDesc('copies_count')
            ->with(['image' => function($q) {
                $q->select('id', 'title', 'thumbnail', 'slug', 'ai_model', 'item_for_sale');
            }])
            ->take(10)
            ->get();

        // Recent copy event stream
        $copyStream = AnalyticsEvent::whereIn('event_name', ['prompt_copy', 'prompt_copy_attempt'])
            ->whereBetween('created_at', [$start, $end])
            ->with(['user:id,username,name', 'image:id,title,thumbnail,slug'])
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'stream_page');

        return view('admin.analytics.prompts', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'totalViews' => $totalViews,
            'totalCopies' => $totalCopies,
            'totalBlockedAttempts' => $totalBlockedAttempts,
            'uniqueCopiers' => $uniqueCopiers,
            'mostViewed' => $mostViewed,
            'mostCopied' => $mostCopied,
            'copyStream' => $copyStream,
        ]);
    }

    /**
     * Pricing & Payment Funnel view.
     */
    public function funnel(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        // Step 1: All Unique Visitors
        $totalVisitors = AnalyticsSession::where('is_bot', 0)
            ->whereBetween('started_at', [$start, $end])
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Step 2: Pricing Page Visitors
        $pricingViews = AnalyticsEvent::where('event_name', 'pricing_view')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Step 3: Checkout Initiated
        $checkoutStarts = AnalyticsEvent::where('event_name', 'checkout_start')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Step 4: Payment Attempted
        $paymentAttempts = AnalyticsEvent::where('event_name', 'payment_attempt')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Step 5: Successful Payment
        $paymentSuccesses = AnalyticsEvent::where('event_name', 'payment_success')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Gateway performance
        $gateways = AnalyticsEvent::where('event_name', 'payment_success')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(function($item) {
                return $item->metadata['payment_gateway'] ?? 'Other';
            })
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total_amount' => $group->sum(function($item) {
                        return (float)($item->metadata['amount'] ?? 0);
                    }),
                ];
            });

        return view('admin.analytics.funnel', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'totalVisitors' => $totalVisitors,
            'pricingViews' => $pricingViews,
            'checkoutStarts' => $checkoutStarts,
            'paymentAttempts' => $paymentAttempts,
            'paymentSuccesses' => $paymentSuccesses,
            'gateways' => $gateways,
        ]);
    }

    /**
     * Users & Retention view.
     */
    public function users(Request $request)
    {
        [$start, $end, $range, $rangeLabel] = $this->parseDateRange($request);

        $newUsers = User::whereBetween('date', [$start, $end])->count();
        $totalRegistered = User::count();

        // Daily registration counts for chart
        $daysCount = $start->diffInDays($end) + 1;
        $intervalDays = max(1, min(60, $daysCount));
        $chartDates = [];
        $chartUsers = [];

        for ($i = 0; $i < $intervalDays; $i++) {
            $d = (clone $start)->addDays($i);
            if ($d > $end) break;
            $dStr = $d->toDateString();
            $dLabel = $d->format('M d');

            $count = User::whereDate('date', $dStr)->count();
            $chartDates[] = "'$dLabel'";
            $chartUsers[] = $count;
        }

        // Recent registrations list
        $recentUsers = User::whereBetween('date', [$start, $end])
            ->orderByDesc('date')
            ->paginate(20);

        return view('admin.analytics.users', [
            'settings' => $this->settings,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'newUsers' => $newUsers,
            'totalRegistered' => $totalRegistered,
            'chartDates' => implode(',', $chartDates),
            'chartUsers' => implode(',', $chartUsers),
            'recentUsers' => $recentUsers,
        ]);
    }

    /**
     * Client Heartbeat Ping endpoint.
     */
    public function ping(Request $request)
    {
        try {
            $visitorId = AnalyticsService::getVisitorId();
            $sessionData = AnalyticsService::getSessionId($visitorId);
            $sessionId = $sessionData['session_id'];

            $session = AnalyticsSession::where('session_id', $sessionId)->first();
            if ($session) {
                $now = Carbon::now();
                $session->last_activity_at = $now;
                if ($session->started_at) {
                    $session->duration_seconds = max(0, $now->diffInSeconds($session->started_at));
                }
                if ($path = $request->input('path')) {
                    $session->exit_page = Str::limit($path, 255, '');
                }
                if ($session->duration_seconds >= 30) {
                    $session->is_engaged = true;
                }
                $session->save();
            }

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Client Interaction Event endpoint.
     */
    public function clientEvent(Request $request)
    {
        $eventName = $request->input('event');
        $validEvents = ['prompt_copy_attempt', 'checkout_start'];

        if (in_array($eventName, $validEvents)) {
            $imageId = $request->input('image_id');
            $metadata = $request->input('metadata', []);
            AnalyticsService::logEvent($eventName, request()->header('referer'), $imageId, (array)$metadata);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid event'], 400);
    }
}
