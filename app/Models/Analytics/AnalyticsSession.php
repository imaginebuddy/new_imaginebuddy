<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AnalyticsSession extends Model
{
    public $timestamps = false;
    protected $table = 'analytics_sessions';
    protected $guarded = [];

    protected $casts = [
        'is_new_visitor' => 'boolean',
        'is_new_session' => 'boolean',
        'is_engaged' => 'boolean',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(AnalyticsEvent::class, 'session_id', 'session_id');
    }

    public function searches()
    {
        return $this->hasMany(AnalyticsSearch::class, 'session_id', 'session_id');
    }

    public function scopeRealUsers($query)
    {
        return $query->where('is_bot', 0);
    }

    public function scopeBots($query)
    {
        return $query->where('is_bot', '>', 0);
    }
}
