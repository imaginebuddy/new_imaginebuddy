<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDailySummary extends Model
{
    protected $table = 'analytics_daily_summary';
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];
}
