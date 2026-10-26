<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSyncLog extends Model
{
    protected $table = 'email_sync_logs';

    protected $fillable = [
        'triggered_by',
        'admin_id',
        'status',
        'bounces_synced',
        'disposables_detected',
        'newly_suppressed',
        'error_details',
        'duration_seconds',
    ];

    protected $casts = [
        'bounces_synced' => 'integer',
        'disposables_detected' => 'integer',
        'newly_suppressed' => 'integer',
        'duration_seconds' => 'float',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
