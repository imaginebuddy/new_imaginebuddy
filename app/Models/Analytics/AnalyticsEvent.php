<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Images;

class AnalyticsEvent extends Model
{
    public $timestamps = false;
    protected $table = 'analytics_events';
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AnalyticsSession::class, 'session_id', 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Images::class, 'image_id');
    }
}
