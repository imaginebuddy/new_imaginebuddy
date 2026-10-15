<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HireInquiry extends Model
{
    protected $table = 'hire_inquiries';

    protected $guarded = ['id'];

    protected $casts = [
        'photoshoot_types' => 'array',
        'intended_use' => 'array',
        'desired_delivery_date' => 'date',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'new' => '<span class="badge bg-primary">New</span>',
            'in_review' => '<span class="badge bg-info text-dark">In Review</span>',
            'contacted' => '<span class="badge bg-warning text-dark">Contacted</span>',
            'quoted' => '<span class="badge bg-purple text-white" style="background-color: #6f42c1;">Quoted</span>',
            'converted' => '<span class="badge bg-success">Converted</span>',
            'archived' => '<span class="badge bg-secondary">Archived</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }
}
