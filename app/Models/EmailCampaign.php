<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $table = 'email_campaigns';

    protected $fillable = [
        'name',
        'subject',
        'preview_text',
        'target_segment',
        'template_preset',
        'content',
        'status',
        'recipients_count',
        'sent_count',
        'processed_count',
        'delivered_count',
        'bounced_count',
        'complained_count',
        'failed_count',
        'unique_opens_count',
        'total_opens_count',
        'unique_clicks_count',
        'total_clicks_count',
        'track_opens',
        'track_clicks',
        'scheduled_at',
        'sent_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'recipients_count' => 'integer',
        'sent_count' => 'integer',
        'processed_count' => 'integer',
        'delivered_count' => 'integer',
        'bounced_count' => 'integer',
        'complained_count' => 'integer',
        'failed_count' => 'integer',
        'unique_opens_count' => 'integer',
        'total_opens_count' => 'integer',
        'unique_clicks_count' => 'integer',
        'total_clicks_count' => 'integer',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
    ];

    /**
     * Relationship to author/admin
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'campaign_id');
    }

    public function clicks()
    {
        return $this->hasMany(CampaignLinkClick::class, 'campaign_id');
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->status === 'sent') {
            return 100;
        }
        if ($this->recipients_count <= 0) {
            return 0;
        }
        return min(100, (int) round(($this->processed_count / $this->recipients_count) * 100));
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->sent_count <= 0) {
            return 0.0;
        }
        return round(($this->delivered_count / $this->sent_count) * 100, 1);
    }

    public function getOpenRateAttribute(): float
    {
        $base = $this->delivered_count > 0 ? $this->delivered_count : $this->sent_count;
        if ($base <= 0) {
            return 0.0;
        }
        return round(($this->unique_opens_count / $base) * 100, 1);
    }

    public function getClickRateAttribute(): float
    {
        $base = $this->unique_opens_count > 0 ? $this->unique_opens_count : $this->delivered_count;
        if ($base <= 0) {
            return 0.0;
        }
        return round(($this->unique_clicks_count / $base) * 100, 1);
    }

    public function getBounceRateAttribute(): float
    {
        if ($this->sent_count <= 0) {
            return 0.0;
        }
        return round(($this->bounced_count / $this->sent_count) * 100, 1);
    }
}
