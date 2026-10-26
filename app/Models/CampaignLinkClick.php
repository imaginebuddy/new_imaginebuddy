<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignLinkClick extends Model
{
    protected $table = 'campaign_link_clicks';

    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'target_url',
        'ip_address',
        'user_agent',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function recipient()
    {
        return $this->belongsTo(CampaignRecipient::class, 'recipient_id');
    }
}
