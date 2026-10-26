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

    /**
     * Resolve photoshoot models associated with this weekly drop.
     */
    public function getPhotoshootsAttribute()
    {
        if (empty($this->photoshoot_ids)) {
            return collect();
        }

        return Photoshoot::whereIn('id', $this->photoshoot_ids)->get();
    }

    /**
     * Human-friendly formatted period label (e.g. Oct 18 – Oct 26, 2026).
     */
    public function getPeriodLabelAttribute(): string
    {
        if ($this->period_start && $this->period_end) {
            return $this->period_start->format('M d') . ' – ' . $this->period_end->format('M d, Y');
        }
        return (string) $this->period_key;
    }
}
