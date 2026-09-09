<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'image',
        'content',
        'designation',
        'company',
        'rating',
        'sort_order',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::exists(config('path.testimonials') . $this->image)) {
            return Storage::url(config('path.testimonials') . $this->image);
        }

        return null;
    }
}
