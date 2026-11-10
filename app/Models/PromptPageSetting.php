<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PromptPageSetting extends Model
{
    protected $table = 'prompt_page_settings';

    protected $guarded = ['id'];

    /**
     * Retrieve the settings instance, cached with fallback defaults.
     */
    public static function getSettings()
    {
        return Cache::remember('prompt_page_settings_singleton', 86400, function () {
            $record = static::first();
            if (!$record) {
                $record = static::create([
                    'hero_badge' => 'Curated AI Prompt Collections',
                    'hero_title' => 'Better prompts for better commercial product work.',
                    'hero_subtitle' => 'Explore production-tested AI photography prompt collections for e-commerce, fashion, and luxury goods. Optimized for Midjourney, Flux, Google Gemini, and ChatGPT.',
                    'banner_title' => 'Done with manual prompts? Let AI do the studio work.',
                    'banner_subtitle' => 'Upload a single raw smartphone photo and transform it into marketplace-ready model and studio catalog visuals in seconds.',
                    'banner_cta_text' => 'Try ImagineBuddy AI Free',
                    'banner_cta_url' => url('register'),
                    'meta_title' => 'AI Product Photography Prompts & Collections | ImagineBuddy',
                    'meta_description' => 'Browse tested AI product photography prompts across ethnic fashion, apparel, jewelry, footwear, and consumer goods. Copy high-converting prompt recipes for Midjourney & Flux.',
                    'meta_keywords' => 'ai prompts, product photography prompts, saree prompts, midjourney prompts, flux prompts, ecommerce ai photography',
                ]);
            }
            return $record;
        });
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!empty($this->cover_image)) {
            $path = 'uploads/prompt-collections/' . $this->cover_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (\Illuminate\Support\Facades\Storage::exists($path)) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }
            return asset('public/' . $path);
        }

        return '';
    }

    /**
     * Clear settings cache on save
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('prompt_page_settings_singleton');
        });

        static::deleted(function () {
            Cache::forget('prompt_page_settings_singleton');
        });
    }
}
