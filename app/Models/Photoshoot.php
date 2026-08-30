<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photoshoot extends Model
{
    protected $table = 'photoshoots';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'user_id',
        'categories_id',
        'prompts_count',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categories_id');
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'photoshoot_id')->where('status', 'active');
    }

    public function allImages()
    {
        return $this->hasMany(Images::class, 'photoshoot_id');
    }
}
