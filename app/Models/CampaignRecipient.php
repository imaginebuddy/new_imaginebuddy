<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignRecipient extends Model
{
    protected $table = 'campaign_recipients';

    protected $fillable = [
        'campaign_id',
        'recipient_email',
        'recipient_name',
        'tracking_token',
        'status',
        'ses_message_id',
        'error_message',
        'sent_at',
        'delivered_at',
        'opened_at',
        'open_count',
        'clicked_at',
        'click_count',
        'bounced_at',
        'bounce_type',
        'complained_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'complained_at' => 'datetime',
        'open_count' => 'integer',
        'click_count' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function clicks()
    {
        return $this->hasMany(CampaignLinkClick::class, 'recipient_id');
    }
}
