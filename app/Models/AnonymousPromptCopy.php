<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnonymousPromptCopy extends Model
{
    protected $table = 'anonymous_prompt_copies';
    public $timestamps = false;

    protected $fillable = [
        'visitor_id',
        'ip_hash',
        'images_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function image()
    {
        return $this->belongsTo(Images::class, 'images_id');
    }
}
