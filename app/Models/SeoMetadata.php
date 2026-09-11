<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'page_key',
        'page_name',
        'route_name',
        'path',
        'type',
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
        'schema_custom',
        'is_active',
        'is_system',
        'seoable_type',
        'seoable_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Cache key for in-memory SEO registry.
     */
    public const CACHE_KEY = 'seo_metadata_registry';

    /**
     * Boot the model and register cache invalidation hooks.
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
        });

        static::deleted(function () {
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * Scope query to active records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to static pages.
     */
    public function scopeStaticPages($query)
    {
        return $query->where('type', 'static_page');
    }

    /**
     * Scope query to dynamic templates.
     */
    public function scopeDynamicTemplates($query)
    {
        return $query->where('type', 'dynamic_template');
    }
}
