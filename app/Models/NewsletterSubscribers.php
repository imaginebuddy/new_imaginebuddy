<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscribers extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'status',
        'verification_token',
        'source_page',
        'ip_address',
        'confirmed_at',
        'unsubscribed_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Relationship to EmailSuppression record if suppressed.
     */
    public function suppression()
    {
        return $this->hasOne(EmailSuppression::class, 'email', 'email');
    }

    /**
     * Scope for active confirmed subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'subscribed');
    }

    /**
     * Scope for pending subscribers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending_verification');
    }

    /**
     * Scope for pending subscribers eligible for a 24h follow-up reminder.
     */
    public function scopePendingReminder($query)
    {
        return $query->where('status', 'pending_verification')
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', now()->subHours(24))
            ->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope for stale pending subscribers (older than 7 days) to auto-expire.
     */
    public function scopeExpiredPending($query)
    {
        return $query->where('status', 'pending_verification')
            ->where('created_at', '<', now()->subDays(7));
    }

    /**
     * Generate a new random token.
     */
    public static function generateToken(): string
    {
        return Str::random(60);
    }
}
