<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageExample extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'images_id',
        'file',
    ];

    public function image()
    {
        return $this->belongsTo(Images::class, 'images_id');
    }
}
