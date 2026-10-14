<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoLandingPageSection extends Model
{
    protected $table = 'seo_landing_page_sections';

    protected $fillable = [
        'landing_page_id',
        'type',
        'badge',
        'heading',
        'subheading',
        'content',
        'settings',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Parent landing page.
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(SeoLandingPage::class, 'landing_page_id');
    }

    /**
     * Items associated directly with this section.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SeoLandingPageItem::class, 'section_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Active items associated with this section.
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }
}
