<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoLandingPageItem extends Model
{
    protected $table = 'seo_landing_page_items';

    protected $touches = ['landingPage'];

    protected $fillable = [
        'landing_page_id',
        'section_id',
        'item_type',
        'item_id',
        'custom_title',
        'custom_badge',
        'custom_notes',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
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
     * Optional parent section.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(SeoLandingPageSection::class, 'section_id');
    }

    /**
     * Resolve related prompt (Images) if item_type is 'prompt'.
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Images::class, 'item_id');
    }

    /**
     * Resolve related photoshoot if item_type is 'photoshoot'.
     */
    public function photoshoot(): BelongsTo
    {
        return $this->belongsTo(Photoshoot::class, 'item_id');
    }

    /**
     * Dynamic getter for target entity.
     */
    public function getTargetEntityAttribute()
    {
        if ($this->item_type === 'prompt') {
            return $this->prompt;
        } elseif ($this->item_type === 'photoshoot') {
            return $this->photoshoot;
        }
        return null;
    }

    /**
     * Dynamic getter for preview image URL (compatible with S3, CloudFront, local storage, prompts, and photoshoots).
     */
    public function getPreviewImageUrlAttribute(): ?string
    {
        if ($this->item_type === 'prompt' && $this->prompt) {
            return $this->prompt->preview_url;
        } elseif ($this->item_type === 'photoshoot' && $this->photoshoot) {
            return $this->photoshoot->thumbnail_url;
        }
        return null;
    }
}
