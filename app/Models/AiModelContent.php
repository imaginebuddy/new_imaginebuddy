<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiModelContent extends Model
{
    protected $table = 'ai_model_contents';

    protected $guarded = ['id'];

    /**
     * Get or instantiate model content by slug with caching
     */
    public static function getBySlug(string $slug, ?string $fallbackName = null): self
    {
        return Cache::remember("ai_model_content_{$slug}", 86400, function () use ($slug, $fallbackName) {
            $record = self::where('slug', $slug)->first();
            $displayName = $fallbackName ?: Str::headline(str_replace('-', ' ', $slug));

            if (!$record) {
                $record = new self([
                    'slug' => $slug,
                    'model_name' => $displayName,
                    'page_title' => $displayName . ' - <span style="color: #00d690;">AI Prompts</span>',
                    'hero_lead' => null, // null means use standard dynamic prompt count formula
                    'seo_title' => $displayName . ' - AI Prompts - ' . (config('settings.title') ?: config('app.name', 'ImagineBuddy')),
                    'meta_description' => 'Explore curated, tested AI prompts designed for ' . $displayName . '. Generate high-end commercial product visuals.',
                    'meta_keywords' => strtolower($displayName) . ' prompts, ' . strtolower($displayName) . ' ai photography, ai product photography, commercial prompts',
                ]);
            }

            return $record;
        });
    }

    /**
     * Clear cache on save/delete
     */
    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("ai_model_content_{$model->slug}");
        });

        static::deleted(function ($model) {
            Cache::forget("ai_model_content_{$model->slug}");
        });
    }
}
