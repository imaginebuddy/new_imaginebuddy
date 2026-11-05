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
        'page_type',
        'route_prefix',
        'icon',
        'header_badge',
        'header_cta_text',
        'header_cta_url',
        'header_cta_subtext',
        'internal_name',
        'title',
        'slug',
        'preview_token',
        'excerpt',
        'featured_image',
        'featured_image_alt',
        'before_image',
        'after_image',
        'before_label',
        'after_label',
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
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($page) {
            \Illuminate\Support\Facades\Cache::forget('footer_cms_navigation');
            if (class_exists(\App\Http\Controllers\SitemapController::class)) {
                \App\Http\Controllers\SitemapController::clearCache();
            }
        });

        static::deleted(function ($page) {
            \Illuminate\Support\Facades\Cache::forget('footer_cms_navigation');
            if (class_exists(\App\Http\Controllers\SitemapController::class)) {
                \App\Http\Controllers\SitemapController::clearCache();
            }
        });
    }

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
            if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
                return $this->featured_image;
            }
            $clean = ltrim(str_replace('public/', '', $this->featured_image), '/');
            if (file_exists(public_path($clean))) {
                return asset('public/' . $clean);
            }
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
     * Helper to get before image (Raw Snapshot) full URL or fallback.
     */
    public function getBeforeImageUrlAttribute(): string
    {
        if (!empty($this->before_image)) {
            if (filter_var($this->before_image, FILTER_VALIDATE_URL)) {
                return $this->before_image;
            }
            $clean = ltrim(str_replace('public/', '', $this->before_image), '/');
            if (file_exists(public_path($clean))) {
                return asset('public/' . $clean);
            }
            $path = config('path.guides', 'uploads/guides/') . $this->before_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
        }
        return asset('public/img/White-Oud-Perfume-01.webp');
    }

    /**
     * Helper to get after image (Campaign Ready) full URL or fallback.
     */
    public function getAfterImageUrlAttribute(): string
    {
        $after = !empty($this->after_image) ? $this->after_image : $this->featured_image;
        if (!empty($after)) {
            if (filter_var($after, FILTER_VALIDATE_URL)) {
                return $after;
            }
            $clean = ltrim(str_replace('public/', '', $after), '/');
            if (file_exists(public_path($clean))) {
                return asset('public/' . $clean);
            }
            $path = config('path.guides', 'uploads/guides/') . $after;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
        }
        return asset('public/img/premium-white-oud-perfume-ad-02.webp');
    }

    /**
     * Determine if page has before/after slider data.
     */
    public function getHasBeforeAfterSliderAttribute(): bool
    {
        return !empty($this->before_image) || (!empty($this->after_image) && !empty($this->featured_image));
    }

    /**
     * Scope query to hub pages.
     */
    public function scopeHubs($query)
    {
        return $query->where('page_type', 'hub');
    }

    /**
     * Scope query to solution pages.
     */
    public function scopeSolutions($query)
    {
        return $query->where('page_type', 'solution');
    }

    /**
     * Scope query by route prefix.
     */
    public function scopeByPrefix($query, ?string $prefix)
    {
        return $query->where('route_prefix', $prefix);
    }

    /**
     * Generate an absolute public URL.
     */
    public function getPublicUrlAttribute(): string
    {
        if (!empty($this->route_prefix)) {
            if ($this->page_type === 'hub') {
                return url($this->route_prefix);
            }
            return url($this->route_prefix . '/' . $this->slug);
        }
        return url('guides/' . $this->slug);
    }

    /**
     * Generate a draft preview URL.
     */
    public function getPreviewUrlAttribute(): string
    {
        if (!empty($this->route_prefix)) {
            return url($this->route_prefix . '/preview/' . $this->preview_token);
        }
        return url('guides/preview/' . $this->preview_token);
    }
}
