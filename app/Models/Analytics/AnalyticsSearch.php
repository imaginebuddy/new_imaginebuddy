<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AnalyticsSearch extends Model
{
    public $timestamps = false;
    protected $table = 'analytics_searches';
    protected $guarded = [];

    protected $casts = [
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
}
