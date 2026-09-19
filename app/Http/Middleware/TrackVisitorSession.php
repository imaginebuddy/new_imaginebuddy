<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class TrackVisitorSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Don't track admin panel, image previews, static file routes, or internal API pings
        $isAdminOrInternal = $request->is(
            'panel*', 
            'panel/admin*', 
            'analytics/ping', 
            'analytics/event', 
            'files/*', 
            'files/preview/*',
            'sitemaps*.xml'
        );

        if (!$isAdminOrInternal) {
            AnalyticsService::getVisitorId();
        }

        $response = $next($request);

        if (!$isAdminOrInternal && $request->isMethod('get') && !$request->expectsJson() && !$request->ajax()) {
            AnalyticsService::trackRequest();
        }

        return $response;
    }
}
