<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UrlRedirect extends Model
{
    protected $table = 'url_redirects';

    const CACHE_KEY = 'url_redirects_cache_map';

    protected $fillable = [
        'source_url',
        'destination_url',
        'redirect_type',
        'status',
        'preserve_query',
        'notes',
        'hits_count',
        'last_used_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'preserve_query' => 'boolean',
        'redirect_type' => 'integer',
        'hits_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    /**
     * Boot model events for automatic cache invalidation.
     */
    protected static function booted()
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Clear active redirects cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Retrieve cached lookup map for active redirects.
     */
    public static function getCachedMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $redirects = self::where('status', true)->get();
            $map = [];
            foreach ($redirects as $r) {
                $normSource = strtolower(self::normalizeSourceUrl($r->source_url));
                $map[$normSource] = [
                    'id' => $r->id,
                    'destination_url' => $r->destination_url,
                    'redirect_type' => (int) $r->redirect_type,
                    'preserve_query' => (bool) $r->preserve_query,
                ];
            }
            return $map;
        });
    }

    /**
     * Normalize source URL for consistent storage and matching.
     */
    public static function normalizeSourceUrl(string $url): string
    {
        $url = trim($url);

        // If a full URL was provided, extract the path and query
        if (preg_match('#^https?://#i', $url)) {
            $parts = parse_url($url);
            $path = $parts['path'] ?? '/';
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';
            $url = $path . $query;
        }

        $qPos = strpos($url, '?');
        if ($qPos !== false) {
            $path = substr($url, 0, $qPos);
            $query = substr($url, $qPos);
        } else {
            $path = $url;
            $query = '';
        }

        // Subdirectory tolerance: if path starts with subfolder matching app path
        $appPath = parse_url(config('app.url'), PHP_URL_PATH);
        if ($appPath && $appPath !== '/' && str_starts_with($path, $appPath)) {
            $path = substr($path, strlen($appPath));
        }

        $path = '/' . ltrim($path, '/');
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        return strtolower($path) . $query;
    }

    /**
     * Normalize destination URL.
     */
    public static function normalizeDestinationUrl(string $url): string
    {
        $url = trim($url);

        // If external URL, keep intact
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        // If full app URL was pasted, strip scheme and domain
        $appUrl = config('app.url');
        if ($appUrl && str_starts_with($url, $appUrl)) {
            $url = substr($url, strlen($appUrl));
        }

        return '/' . ltrim($url, '/');
    }

    /**
     * Detect self-redirects and cyclic redirect loops.
     */
    public static function detectLoop(string $source, string $destination, ?int $ignoreId = null): ?string
    {
        $normSource = self::normalizeSourceUrl($source);
        $normDest = self::normalizeDestinationUrl($destination);

        // Self-redirect check
        if (strtolower($normSource) === strtolower($normDest)) {
            return 'The destination URL cannot be identical to the source URL.';
        }

        // Cyclic loop check up to 6 hops
        $visited = [$normSource];
        $current = $normDest;

        for ($i = 0; $i < 6; $i++) {
            if (preg_match('#^https?://#i', $current)) {
                break;
            }

            $currentPath = self::normalizeSourceUrl($current);
            if (in_array($currentPath, $visited)) {
                return "Redirect loop detected: [{$normSource} -> {$normDest}] forms a cyclic redirect chain.";
            }
            $visited[] = $currentPath;

            $nextRedirect = self::where('source_url', $currentPath)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->first();

            if (!$nextRedirect) {
                break;
            }

            $current = $nextRedirect->destination_url;
        }

        return null;
    }

    /**
     * Build final destination URL with query preservation.
     */
    public static function buildDestinationUrl(string $destination, ?string $incomingQuery = null, bool $preserveQuery = true): string
    {
        $isExternal = preg_match('#^https?://#i', $destination);
        $baseUrl = $isExternal ? $destination : url($destination);

        if (!$preserveQuery || empty($incomingQuery)) {
            return $baseUrl;
        }

        $destParts = parse_url($baseUrl);
        $destQuery = [];
        if (!empty($destParts['query'])) {
            parse_str($destParts['query'], $destQuery);
        }

        $inQuery = [];
        parse_str($incomingQuery, $inQuery);

        $mergedQuery = array_merge($destQuery, $inQuery);
        $queryString = http_build_query($mergedQuery);

        $cleanBase = explode('?', $baseUrl)[0];
        return $queryString ? $cleanBase . '?' . $queryString : $cleanBase;
    }
}
