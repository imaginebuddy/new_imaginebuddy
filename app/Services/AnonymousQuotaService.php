<?php

namespace App\Services;

use App\Models\AnonymousPromptCopy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AnonymousQuotaService
{
    /**
     * Fixed hard-set monthly allowance for anonymous / non-logged-in visitors.
     */
    public const MONTHLY_LIMIT = 3;

    /**
     * Retrieve or initialize the visitor UUID from AnalyticsService.
     */
    public static function getVisitorId(): string
    {
        return AnalyticsService::getVisitorId();
    }

    /**
     * Generate a privacy-preserving monthly IP hash for abuse prevention.
     */
    public static function getIpHash(): string
    {
        $ip = request()->header('CF-Connecting-IP') ?: request()->ip() ?: '0.0.0.0';
        $salt = config('app.key') ?: 'imagine_buddy_salt';
        $month = Carbon::now('UTC')->format('Y-m');

        return hash('sha256', $ip . '|' . $salt . '|' . $month);
    }

    /**
     * Get the count of prompt copies consumed by this anonymous visitor in current calendar month.
     */
    public function getMonthlyUsed(): int
    {
        $visitorId = self::getVisitorId();
        $ipHash = self::getIpHash();
        $startOfMonth = Carbon::now('UTC')->startOfMonth();

        // 1. Database Check (if table exists)
        try {
            if (Schema::hasTable('anonymous_prompt_copies')) {
                return (int) DB::table('anonymous_prompt_copies')
                    ->where('created_at', '>=', $startOfMonth)
                    ->where(function ($q) use ($visitorId, $ipHash) {
                        $q->where('visitor_id', $visitorId)
                          ->orWhere('ip_hash', $ipHash);
                    })
                    ->count();
            }
        } catch (\Throwable $e) {
            Log::warning('AnonymousQuotaService DB query failed, using cache fallback: ' . $e->getMessage());
        }

        // 2. Cache / Cookie Fallback
        $cacheKey = 'anon_prompt_copies_' . md5($visitorId . '|' . $ipHash . '|' . $startOfMonth->format('Y-m'));
        return (int) Cache::get($cacheKey, 0);
    }

    /**
     * Calculate remaining free copies for current calendar month.
     */
    public function remainingCopies(): int
    {
        return max(0, self::MONTHLY_LIMIT - $this->getMonthlyUsed());
    }

    /**
     * Check if the visitor is allowed to copy another free prompt.
     */
    public function canCopy(): bool
    {
        return $this->getMonthlyUsed() < self::MONTHLY_LIMIT;
    }

    /**
     * Check for debounced copy (within 10 seconds for same image by same anonymous visitor).
     */
    public function isDebouncedDuplicate(int $imageId): bool
    {
        $visitorId = self::getVisitorId();
        $threshold = Carbon::now('UTC')->subSeconds(10);

        try {
            if (Schema::hasTable('anonymous_prompt_copies')) {
                return DB::table('anonymous_prompt_copies')
                    ->where('images_id', $imageId)
                    ->where('visitor_id', $visitorId)
                    ->where('created_at', '>=', $threshold)
                    ->exists();
            }
        } catch (\Throwable $e) {
            // Ignore in fallback mode
        }

        return false;
    }

    /**
     * Record an anonymous prompt copy action.
     */
    public function recordCopy(int $imageId): void
    {
        $visitorId = self::getVisitorId();
        $ipHash = self::getIpHash();
        $startOfMonth = Carbon::now('UTC')->startOfMonth();
        $now = Carbon::now('UTC');

        // 1. Attempt Database Record
        try {
            if (Schema::hasTable('anonymous_prompt_copies')) {
                DB::table('anonymous_prompt_copies')->insert([
                    'visitor_id' => $visitorId,
                    'ip_hash'    => $ipHash,
                    'images_id'  => $imageId,
                    'created_at' => $now,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Could not insert anonymous_prompt_copy record: ' . $e->getMessage());
        }

        // 2. Sync Cache Record (35 days TTL to guarantee entire month)
        $cacheKey = 'anon_prompt_copies_' . md5($visitorId . '|' . $ipHash . '|' . $startOfMonth->format('Y-m'));
        $current = (int) Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $current + 1, Carbon::now('UTC')->endOfMonth()->addDays(2));
    }
}
