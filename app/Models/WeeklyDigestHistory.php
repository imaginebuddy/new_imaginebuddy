<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyDigestHistory extends Model
{
    protected $table = 'weekly_digest_history';

    protected $fillable = [
        'campaign_id',
        'period_key',
        'period_start',
        'period_end',
        'photoshoots_count',
        'photoshoot_ids',
        'recipients_count',
        'status',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'photoshoots_count' => 'integer',
        'recipients_count' => 'integer',
        'photoshoot_ids' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }
}
