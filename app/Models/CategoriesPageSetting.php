<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CategoriesPageSetting extends Model
{
    protected $table = 'categories_page_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'cta_banner_enabled' => 'boolean',
    ];

    public const CACHE_KEY = 'categories_page_settings_data';

    /**
     * Default values for /categories listing page.
     */
    public static function defaultValues(): array
    {
        return [
            'page_title' => 'Categories',
            'page_subtitle' => 'Browse by Category',
            'tags_title' => 'Tags',
            'tags_subtitle' => 'Explore by popular keywords and tags',
            'category_search_placeholder' => 'Search categories...',
            'tag_search_placeholder' => 'Search tags...',
            'meta_title' => 'Categories - AI Product Photography Prompts | ImagineBuddy',
            'meta_description' => 'Browse best curated AI product photography prompt categories. Explore skincare, jewellery, food, electronics, and commercial mockup styles.',
            'meta_keywords' => 'product photography categories, ai prompts, midjourney categories, chatgpt photography prompts',
            'cta_banner_enabled' => false,
            'cta_banner_title' => 'Need Custom AI Photoshoots For Your Brand?',
            'cta_banner_desc' => 'Hire our team to create bespoke AI-assisted commercial photoshoots tailored to your exact SKU and creative direction.',
            'cta_banner_btn_text' => 'Explore Custom Service',
            'cta_banner_btn_url' => 'hire',
        ];
    }

    /**
     * Retrieve the active settings singleton with cache and automatic seeding.
     */
    public static function getSettings(): self
    {
        return Cache::remember(self::CACHE_KEY, 86400, function () {
            $setting = self::first();
            if (!$setting) {
                $setting = self::create(self::defaultValues());
            }
            return $setting;
        });
    }

    /**
     * Safe getter with fallback to defaults.
     */
    public function getVal(string $key, $customFallback = null)
    {
        $val = $this->{$key};
        if ($val !== null && $val !== '') {
            return $val;
        }

        if ($customFallback !== null) {
            return $customFallback;
        }

        $defaults = self::defaultValues();
        return $defaults[$key] ?? null;
    }

    /**
     * Clear the cache when settings are saved or deleted.
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
}
