<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSuppression extends Model
{
    protected $table = 'email_suppressions';

    protected $fillable = [
        'email',
        'reason',
        'channel_scope',
        'soft_bounce_count',
        'last_bounce_at',
        'source_stream',
        'raw_payload',
    ];

    protected $casts = [
        'soft_bounce_count' => 'integer',
        'last_bounce_at' => 'datetime',
    ];

    /**
     * Check if an email address is suppressed for a specific channel.
     *
     * Channels:
     * - 'updates' / 'marketing': Suppressed if ANY suppression record exists.
     * - 'auth' / 'transactional': Suppressed ONLY if channel_scope is 'all' (hard bounces, complaints, disposable domains, admin blocked).
     *   Manual unsubscribes from marketing will NOT block critical password resets or 2FA OTP codes.
     */
    public static function isSuppressed(?string $email, string $channel = 'updates'): bool
    {
        if (empty($email)) {
            return false;
        }

        $normalized = strtolower(trim($email));

        $record = static::where('email', $normalized)->first();
        if (!$record) {
            return false;
        }

        // If checking auth/transactional channel:
        if (in_array(strtolower($channel), ['auth', 'transactional', 'security'])) {
            return $record->channel_scope === 'all';
        }

        // For marketing/updates channel, any suppression record applies
        return true;
    }

    /**
     * Add or update an email in the suppression list.
     */
    public static function suppress(
        string $email,
        string $reason = 'hard_bounce',
        string $stream = 'updates',
        $payload = null,
        ?string $channelScope = null
    ): self {
        $normalizedEmail = strtolower(trim($email));

        // Default channel scope depending on reason
        if ($channelScope === null) {
            $channelScope = in_array($reason, ['hard_bounce', 'complaint', 'admin_suppressed', 'disposable'])
                ? 'all'
                : 'marketing_only';
        }

        $rawPayload = is_array($payload) || is_object($payload)
            ? json_encode($payload)
            : (string) $payload;

        return static::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'reason' => $reason,
                'channel_scope' => $channelScope,
                'source_stream' => $stream,
                'raw_payload' => $rawPayload,
                'last_bounce_at' => now(),
            ]
        );
    }

    /**
     * Record a temporary soft bounce with retry threshold.
     *
     * Increments the soft bounce count. If it reaches the threshold (default: 3),
     * automatically promotes the address to permanent suppression.
     *
     * Returns true if promoted to suppressed, false if still below threshold.
     */
    public static function recordSoftBounce(
        string $email,
        string $stream = 'updates',
        $payload = null,
        int $threshold = 3
    ): bool {
        $normalizedEmail = strtolower(trim($email));

        $record = static::where('email', $normalizedEmail)->first();

        $currentCount = $record ? $record->soft_bounce_count : 0;
        $newCount = $currentCount + 1;

        if ($newCount >= $threshold) {
            // Threshold reached: promote to full suppression
            static::suppress(
                $normalizedEmail,
                'hard_bounce',
                $stream,
                $payload,
                'all'
            );
            return true;
        }

        // Below threshold: record warning in database without blocking
        $rawPayload = is_array($payload) || is_object($payload)
            ? json_encode($payload)
            : (string) $payload;

        static::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'reason' => 'soft_bounce',
                'channel_scope' => 'marketing_only',
                'soft_bounce_count' => $newCount,
                'source_stream' => $stream,
                'raw_payload' => $rawPayload,
                'last_bounce_at' => now(),
            ]
        );

        return false;
    }
}
