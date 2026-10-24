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
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

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
     * Generate a new random token.
     */
    public static function generateToken(): string
    {
        return Str::random(60);
    }
}
