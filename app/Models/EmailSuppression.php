<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSuppression extends Model
{
    protected $table = 'email_suppressions';

    protected $fillable = [
        'email',
        'reason',
        'source_stream',
        'raw_payload',
    ];

    /**
     * Check if an email address is suppressed.
     */
    public static function isSuppressed(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        return static::where('email', strtolower(trim($email)))->exists();
    }

    /**
     * Add or update an email in the suppression list.
     */
    public static function suppress(string $email, string $reason = 'hard_bounce', string $stream = 'updates', $payload = null): self
    {
        $normalizedEmail = strtolower(trim($email));

        $rawPayload = is_array($payload) || is_object($payload)
            ? json_encode($payload)
            : (string) $payload;

        return static::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'reason' => $reason,
                'source_stream' => $stream,
                'raw_payload' => $rawPayload,
            ]
        );
    }
}
