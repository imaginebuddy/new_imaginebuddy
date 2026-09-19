<?php

namespace App\Services;

use App\Models\Analytics\AnalyticsSession;
use App\Models\Analytics\AnalyticsEvent;
use App\Models\Analytics\AnalyticsSearch;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Jenssegers\Agent\Facades\Agent;

class AnalyticsService
{
    const COOKIE_VISITOR = '_ib_vid';
    const COOKIE_SESSION = '_ib_sid';
    const SESSION_TIMEOUT_MINUTES = 30;

    /**
     * Get or initialize the persistent visitor ID (UUIDv4).
     */
    public static function getVisitorId(): string
    {
        $visitorId = request()->cookie(self::COOKIE_VISITOR);

        if (!$visitorId || !Str::isUuid($visitorId)) {
            $visitorId = (string) Str::uuid();
            Cookie::queue(Cookie::make(self::COOKIE_VISITOR, $visitorId, 60 * 24 * 365, '/', null, false, false));
        }

        return $visitorId;
    }

    /**
     * Get or initialize the active session ID.
     */
    public static function getSessionId(string $visitorId): array
    {
        $userId = auth()->id() ?: null;
        $session = null;
        $threshold = Carbon::now()->subMinutes(self::SESSION_TIMEOUT_MINUTES);

        // 1. Check by session cookie
        $cookieSessionId = request()->cookie(self::COOKIE_SESSION);
        if ($cookieSessionId) {
            $session = AnalyticsSession::where('session_id', $cookieSessionId)
                ->where('last_activity_at', '>=', $threshold)
                ->first();
        }

        // 2. If not found by cookie, reuse active session of same user_id in last 30 min
        if (!$session && $userId) {
            $session = AnalyticsSession::where('user_id', $userId)
                ->where('last_activity_at', '>=', $threshold)
                ->orderByDesc('last_activity_at')
                ->first();
        }

        // 3. If not found, reuse active session of same visitor_id in last 30 min
        if (!$session && $visitorId) {
            $session = AnalyticsSession::where('visitor_id', $visitorId)
                ->where('last_activity_at', '>=', $threshold)
                ->orderByDesc('last_activity_at')
                ->first();
        }

        $isNewSession = false;
        $isNewVisitor = false;

        if (!$session) {
            $sessionId = (string) Str::uuid();
            $isNewSession = true;
            Cookie::queue(Cookie::make(self::COOKIE_SESSION, $sessionId, self::SESSION_TIMEOUT_MINUTES, '/', null, false, false));

            $hasPriorSessions = AnalyticsSession::where('visitor_id', $visitorId)
                ->orWhere(function($q) use ($userId) {
                    if ($userId) $q->where('user_id', $userId);
                })->exists();
            $isNewVisitor = !$hasPriorSessions;
        } else {
            $sessionId = $session->session_id;
            Cookie::queue(Cookie::make(self::COOKIE_SESSION, $sessionId, self::SESSION_TIMEOUT_MINUTES, '/', null, false, false));
        }

        return [
            'session_id' => $sessionId,
            'is_new_session' => $isNewSession,
            'is_new_visitor' => $isNewVisitor,
            'current_session' => $session,
        ];
    }

    /**
     * Track an incoming page request and update session attributes.
     */
    public static function trackRequest(): ?AnalyticsSession
    {
        try {
            $req = request();

            // Ignore admin panel, static assets, favicon, api pings
            if ($req->is('panel*', 'panel/admin*', 'analytics/ping', 'analytics/event', 'public/*', 'vendor/*', 'css/*', 'js/*', 'images/*', 'uploads/*')) {
                return null;
            }

            $visitorId = self::getVisitorId();
            $sessionData = self::getSessionId($visitorId);
            $sessionId = $sessionData['session_id'];

            // Bot detection via Agent facade
            $isRobot = false;
            $botName = null;
            try {
                if (\Phattarachai\LaravelMobileDetect\Facades\Agent::isRobot()) {
                    $isRobot = true;
                    $botName = \Phattarachai\LaravelMobileDetect\Facades\Agent::robot() ?: 'Unknown Bot';
                }
            } catch (\Throwable $e) {
                $ua = strtolower($req->userAgent() ?? '');
                if (empty($ua) || preg_match('/(bot|crawl|spider|slurp|facebookexternalhit)/i', $ua)) {
                    $isRobot = true;
                    $botName = 'Generic Crawler';
                }
            }

            $isBotVal = $isRobot ? 1 : 0;
            $now = Carbon::now();
            $currentUrl = Str::limit($req->fullUrl(), 255, '');
            $path = Str::limit($req->path(), 255, '');

            $session = $sessionData['current_session'] ?: AnalyticsSession::where('session_id', $sessionId)->first();

            if (!$session) {
                // Device detection
                $deviceType = 'desktop';
                try {
                    if (\Phattarachai\LaravelMobileDetect\Facades\Agent::isMobile()) {
                        $deviceType = 'mobile';
                    } elseif (\Phattarachai\LaravelMobileDetect\Facades\Agent::isTablet()) {
                        $deviceType = 'tablet';
                    }
                    $browser = Str::limit(\Phattarachai\LaravelMobileDetect\Facades\Agent::browser() ?: 'Other', 50, '');
                    $os = Str::limit(\Phattarachai\LaravelMobileDetect\Facades\Agent::platform() ?: 'Other', 50, '');
                } catch (\Throwable $e) {
                    $browser = 'Unknown';
                    $os = 'Unknown';
                }

                // Referrer & UTM
                $referrer = $req->header('referer');
                $referrerDomain = null;
                if ($referrer) {
                    $host = parse_url($referrer, PHP_URL_HOST);
                    if ($host && $host !== $req->getHost()) {
                        $referrerDomain = Str::limit($host, 150, '');
                    }
                }

                $countryCode = session('user_country') ?: null;

                $session = AnalyticsSession::create([
                    'session_id' => $sessionId,
                    'visitor_id' => $visitorId,
                    'user_id' => auth()->id() ?: null,
                    'is_new_visitor' => $sessionData['is_new_visitor'],
                    'is_new_session' => true,
                    'is_bot' => $isBotVal,
                    'bot_name' => $botName,
                    'entry_page' => $path,
                    'exit_page' => $path,
                    'referrer' => Str::limit($referrer, 500, ''),
                    'referrer_domain' => $referrerDomain,
                    'utm_source' => Str::limit($req->get('utm_source'), 100, ''),
                    'utm_medium' => Str::limit($req->get('utm_medium'), 100, ''),
                    'utm_campaign' => Str::limit($req->get('utm_campaign'), 100, ''),
                    'device_type' => $deviceType,
                    'browser' => $browser,
                    'os' => $os,
                    'country_code' => $countryCode,
                    'page_views_count' => 1,
                    'duration_seconds' => 0,
                    'is_engaged' => false,
                    'started_at' => $now,
                    'last_activity_at' => $now,
                ]);
            } else {
                // Update existing session
                $session->page_views_count = $session->page_views_count + 1;
                $session->exit_page = $path;
                $session->last_activity_at = $now;

                if (auth()->check() && !$session->user_id) {
                    $session->user_id = auth()->id();
                }

                if ($session->started_at) {
                    $session->duration_seconds = max(0, $now->diffInSeconds($session->started_at));
                }

                if ($session->page_views_count >= 2 || $session->duration_seconds >= 30) {
                    $session->is_engaged = true;
                }

                $session->save();
            }

            // Record page_view event (excluding pure AJAX partial calls)
            if (!$req->ajax()) {
                self::logEvent('page_view', $currentUrl);
            }

            return $session;
        } catch (\Throwable $e) {
            \Log::warning('Analytics tracking error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log an interaction event.
     */
    public static function logEvent(string $eventName, ?string $pageUrl = null, ?int $imageId = null, array $metadata = []): ?AnalyticsEvent
    {
        try {
            $visitorId = self::getVisitorId();
            $sessionData = self::getSessionId($visitorId);
            $sessionId = $sessionData['session_id'];

            $url = $pageUrl ?: Str::limit(request()->fullUrl(), 255, '');

            // Mark session as engaged on high-intent events
            if (in_array($eventName, ['prompt_copy', 'pricing_view', 'checkout_start', 'payment_attempt', 'payment_success'])) {
                AnalyticsSession::where('session_id', $sessionId)->update(['is_engaged' => true]);
            }

            return AnalyticsEvent::create([
                'session_id' => $sessionId,
                'visitor_id' => $visitorId,
                'user_id' => auth()->id() ?: null,
                'event_name' => $eventName,
                'page_url' => Str::limit($url, 255, ''),
                'image_id' => $imageId,
                'metadata' => !empty($metadata) ? $metadata : null,
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Analytics event log error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log a search query with privacy sanitization.
     */
    public static function logSearch(string $query, int $resultsCount, ?string $tier = null, ?string $aiModel = null): ?AnalyticsSearch
    {
        try {
            $cleanQuery = trim($query);
            if (empty($cleanQuery)) {
                return null;
            }

            // Privacy sanitization: strip email patterns
            $cleanQuery = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[email]', $cleanQuery);

            $visitorId = self::getVisitorId();
            $sessionData = self::getSessionId($visitorId);
            $sessionId = $sessionData['session_id'];

            return AnalyticsSearch::create([
                'session_id' => $sessionId,
                'visitor_id' => $visitorId,
                'user_id' => auth()->id() ?: null,
                'query' => Str::limit($cleanQuery, 255, ''),
                'results_count' => $resultsCount,
                'tier_filter' => $tier ? Str::limit($tier, 20, '') : null,
                'ai_model_filter' => $aiModel ? Str::limit($aiModel, 50, '') : null,
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Analytics search log error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Link all prior anonymous sessions of a visitor to a newly authenticated user.
     */
    public static function stitchUser(int $userId): void
    {
        try {
            $visitorId = request()->cookie(self::COOKIE_VISITOR);
            if ($visitorId) {
                AnalyticsSession::where('visitor_id', $visitorId)
                    ->whereNull('user_id')
                    ->update(['user_id' => $userId]);

                AnalyticsEvent::where('visitor_id', $visitorId)
                    ->whereNull('user_id')
                    ->update(['user_id' => $userId]);

                AnalyticsSearch::where('visitor_id', $visitorId)
                    ->whereNull('user_id')
                    ->update(['user_id' => $userId]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Analytics user stitch error: ' . $e->getMessage());
        }
    }
}
