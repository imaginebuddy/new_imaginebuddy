<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class SeoLandingPage extends Model
{
    protected $table = 'seo_landing_pages';

    protected $fillable = [
        'user_id',
        'category_id',
        'internal_name',
        'title',
        'slug',
        'preview_token',
        'excerpt',
        'featured_image',
        'featured_image_alt',
        'target_audience',
        'primary_keyword',
        'secondary_keywords',
        'search_intent',
        'status',
        'published_at',
        'scheduled_for',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'schema_type',
        'enable_table_of_contents',
        'enable_prompt_copying',
        'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'enable_table_of_contents' => 'boolean',
        'enable_prompt_copying' => 'boolean',
        'views_count' => 'integer',
    ];

    /**
     * Author / Creator relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Primary topic category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    /**
     * Content sections belonging to this landing page.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(SeoLandingPageSection::class, 'landing_page_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Active sections for public display.
     */
    public function activeSections(): HasMany
    {
        return $this->sections()->where('is_active', true);
    }

    /**
     * Connected items (prompts/photoshoots/custom images).
     */
    public function items(): HasMany
    {
        return $this->hasMany(SeoLandingPageItem::class, 'landing_page_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Active connected items for public display.
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    /**
     * Scope query to published landing pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Helper to get featured image full URL or fallback.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        if (!empty($this->featured_image)) {
            $path = config('path.guides', 'uploads/guides/') . $this->featured_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
        }
        return url('public/img', config('settings.logo_light', 'logo.png'));
    }

    /**
     * Generate an absolute public URL.
     */
    public function getPublicUrlAttribute(): string
    {
        return url('guides/' . $this->slug);
    }

    /**
     * Generate a draft preview URL.
     */
    public function getPreviewUrlAttribute(): string
    {
        return url('guides/preview/' . $this->preview_token);
    }
}
