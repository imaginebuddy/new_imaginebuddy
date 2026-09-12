<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClientLogo extends Model
{
    protected $table = 'client_logos';

    protected $fillable = [
        'name',
        'image',
        'website_url',
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
        $path = config('path.client_logos', 'uploads/logos/');
        if ($this->image && Storage::exists($path . $this->image)) {
            return Storage::url($path . $this->image);
        }

        return null;
    }
}
