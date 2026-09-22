<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UrlRedirect;

class RedirectUrlsMiddleware
{
    /**
     * Paths and prefixes that must never be intercepted by URL redirects.
     */
    protected array $excludedPrefixes = [
        'panel',
        'panel/*',
        'api',
        'api/*',
        'oauth',
        'oauth/*',
        'webhook',
        'webhook/*',
        'payment/*',
        'download',
        'download/*',
        'subscription/stock/*',
        'files/*',
        'assets/*',
        'public/*',
        'login',
        'logout',
        'register',
        'password/*',
        'ajax/*',
        'installer/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Only process GET and HEAD requests
        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            return $next($request);
        }

        // 2. Bypass excluded / administrative / API / system routes
        if ($request->is($this->excludedPrefixes)) {
            return $next($request);
        }

        // 3. Normalize current request path
        $path = '/' . trim($request->path(), '/');
        $pathLower = strtolower($path);
        $queryString = $request->getQueryString();
        $pathWithQuery = $queryString ? ($pathLower . '?' . $queryString) : $pathLower;

        // 4. Retrieve cached active redirects map (O(1) lookup)
        $map = UrlRedirect::getCachedMap();
        if (empty($map)) {
            return $next($request);
        }

        // 5. Check for matching redirect (priority: path+query, then path)
        $matched = null;
        if (isset($map[$pathWithQuery])) {
            $matched = $map[$pathWithQuery];
        } elseif (isset($map[$pathLower])) {
            $matched = $map[$pathLower];
        }

        if (!$matched) {
            return $next($request);
        }

        // 6. Build final destination URL
        $destination = UrlRedirect::buildDestinationUrl(
            $matched['destination_url'],
            $queryString,
            $matched['preserve_query']
        );

        // 7. Increment hit count & record timestamp
        try {
            UrlRedirect::where('id', $matched['id'])->increment('hits_count');
            UrlRedirect::where('id', $matched['id'])->update(['last_used_at' => now()]);
        } catch (\Throwable $e) {
            // Keep redirect working even if hit counter write fails
        }

        // 8. Execute redirect
        return redirect()->to($destination, $matched['redirect_type']);
    }
}
