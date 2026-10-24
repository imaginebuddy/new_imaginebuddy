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
        'scheduled_at',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'recipients_count' => 'integer',
        'sent_count' => 'integer',
    ];

    /**
     * Relationship to author/admin
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
