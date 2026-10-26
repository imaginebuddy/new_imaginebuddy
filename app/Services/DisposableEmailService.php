<?php

namespace App\Services;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DisposableEmailService
{
    /**
     * Cache key for in-memory O(1) set lookup.
     */
    protected const CACHE_KEY = 'disposable_email_domains_set';

    /**
     * Check if an email address belongs to a known disposable email provider.
     */
    public static function isDisposable(?string $email): bool
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = strtolower(trim(substr(strrchr($email, "@"), 1)));
        if (empty($domain)) {
            return false;
        }

        $domainsSet = static::getDomainSet();

        return isset($domainsSet[$domain]);
    }

    /**
     * Get flip-hashed set of disposable domains for O(1) instant lookup.
     */
    public static function getDomainSet(): array
    {
        return Cache::remember(self::CACHE_KEY, 86400, function () {
            $domains = config('disposable_domains.domains', []);

            $set = [];
            foreach ($domains as $d) {
                $clean = strtolower(trim($d));
                if (!empty($clean)) {
                    $set[$clean] = true;
                }
            }

            return $set;
        });
    }

    /**
     * Clear the cached domain set (e.g. after adding custom domains).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Audit existing registered users and newsletter subscribers for disposable domains.
     *
     * Non-destructive policy:
     * - Flags user record with is_disposable_email = 1.
     * - Revokes marketing opt-in (marketing_opt_in = 0).
     * - Adds email to email_suppressions with reason = 'disposable', channel_scope = 'all'.
     * - Does NOT delete user account, photos, or purchase history.
     */
    public static function auditExistingUsers(): array
    {
        $domainSet = static::getDomainSet();
        $disposableCount = 0;
        $usersAudited = 0;

        // 1. Audit Users table
        User::chunk(200, function ($users) use ($domainSet, &$disposableCount, &$usersAudited) {
            foreach ($users as $user) {
                $usersAudited++;
                $email = strtolower(trim($user->email));
                $domain = substr(strrchr($email, "@"), 1);

                if (!empty($domain) && isset($domainSet[$domain])) {
                    $disposableCount++;

                    $user->update([
                        'is_disposable_email' => 1,
                        'marketing_opt_in' => 0,
                    ]);

                    EmailSuppression::suppress(
                        $email,
                        'disposable',
                        'audit',
                        ['detected_domain' => $domain, 'user_id' => $user->id],
                        'all'
                    );
                }
            }
        });

        // 2. Audit Newsletter Subscribers table
        $subscribersAudited = 0;
        $subscribersFlagged = 0;

        NewsletterSubscribers::chunk(200, function ($subscribers) use ($domainSet, &$subscribersAudited, &$subscribersFlagged) {
            foreach ($subscribers as $sub) {
                $subscribersAudited++;
                $email = strtolower(trim($sub->email));
                $domain = substr(strrchr($email, "@"), 1);

                if (!empty($domain) && isset($domainSet[$domain])) {
                    $subscribersFlagged++;

                    $sub->update([
                        'status' => 'unsubscribed',
                        'unsubscribed_at' => now(),
                    ]);

                    EmailSuppression::suppress(
                        $email,
                        'disposable',
                        'audit_newsletter',
                        ['detected_domain' => $domain, 'subscriber_id' => $sub->id],
                        'all'
                    );
                }
            }
        });

        Log::info("Disposable email audit completed: {$disposableCount} users and {$subscribersFlagged} subscribers flagged.");

        return [
            'users_audited' => $usersAudited,
            'users_flagged' => $disposableCount,
            'subscribers_audited' => $subscribersAudited,
            'subscribers_flagged' => $subscribersFlagged,
            'total_flagged' => $disposableCount + $subscribersFlagged,
        ];
    }
}
