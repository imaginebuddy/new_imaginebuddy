<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromptCollection extends Model
{
    protected $table = 'prompt_collections';

    protected $guarded = ['id'];

    protected $casts = [
        'creative_guide' => 'array',
        'faqs' => 'array',
        'is_featured' => 'boolean',
        'show_shortcut_section' => 'boolean',
        'shortcut_features' => 'array',
        'show_metrics_section' => 'boolean',
        'metrics_items' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });

        static::saved(function () {
            if (class_exists(\App\Http\Controllers\SitemapController::class)) {
                \App\Http\Controllers\SitemapController::clearCache();
            }
        });

        static::deleted(function ($model) {
            if (class_exists(\App\Http\Controllers\SitemapController::class)) {
                \App\Http\Controllers\SitemapController::clearCache();
            }

            if (!empty($model->cover_image)) {
                $path = 'uploads/prompt-collections/' . $model->cover_image;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                if (file_exists(public_path($path))) {
                    @unlink(public_path($path));
                }
            }

            if (!empty($model->raw_image)) {
                $path = 'uploads/prompt-collections/before/' . $model->raw_image;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                if (file_exists(public_path($path))) {
                    @unlink(public_path($path));
                }
            }

            if (!empty($model->transformed_image)) {
                $path = 'uploads/prompt-collections/after/' . $model->transformed_image;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                if (file_exists(public_path($path))) {
                    @unlink(public_path($path));
                }
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function items()
    {
        return $this->hasMany(PromptCollectionItem::class, 'prompt_collection_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    public function prompts()
    {
        return $this->belongsToMany(Images::class, 'prompt_collection_items', 'prompt_collection_id', 'image_id')
            ->withPivot(['custom_shot_type', 'custom_prompt_override', 'sort_order'])
            ->withTimestamps()
            ->orderBy('prompt_collection_items.sort_order', 'asc');
    }

    public function activePrompts()
    {
        return $this->belongsToMany(Images::class, 'prompt_collection_items', 'prompt_collection_id', 'image_id')
            ->where('images.status', 'active')
            ->withPivot(['custom_shot_type', 'custom_prompt_override', 'sort_order'])
            ->withTimestamps()
            ->orderBy('prompt_collection_items.sort_order', 'asc');
    }

    public function getUrlAttribute(): string
    {
        return url('prompts/' . $this->slug);
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!empty($this->cover_image)) {
            $path = 'uploads/prompt-collections/' . $this->cover_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
            return asset('public/' . $path);
        }

        // Fallback to first prompt preview
        $firstPrompt = $this->activePrompts()->first();
        if ($firstPrompt) {
            if (!empty($firstPrompt->preview)) {
                return Storage::url(config('path.preview') . $firstPrompt->preview);
            }
            if (!empty($firstPrompt->thumbnail)) {
                return Storage::url(config('path.thumbnail') . $firstPrompt->thumbnail);
            }
        }

        return asset('public/img/thumbnail-default.jpg');
    }

    public function getDefaultFaqs(): array
    {
        $title = $this->title ?: 'Commercial Product';
        $engine = $this->ai_engine ?: 'Midjourney, Google Gemini and Flux';

        return [
            [
                'question' => "Which AI generators are compatible with these {$title}?",
                'answer' => "Every prompt in this collection has been optimized and tested across {$engine}. They simulate realistic studio focal lengths (50mm–85mm), octabox lighting, and true-to-life surface textures."
            ],
            [
                'question' => "Can I use these prompts for commercial marketplace listings (Amazon, Shopify, Myntra)?",
                'answer' => "Yes. The prompts are specifically engineered for commercial catalog photography, ad creatives, and high-converting e-commerce listings in compliance with major marketplace guidelines."
            ],
            [
                'question' => "How do I maintain consistent color and fabric textures across angles?",
                'answer' => "Include specific material descriptors (e.g. 'raw mulberry silk weave', 'brushed stainless steel', 'diffused 5600K studio strobe') and maintain fixed aspect ratio tags such as --ar 4:5 across Midjourney and Flux."
            ],
            [
                'question' => "What is the difference between copying prompts and using ImagineBuddy AI?",
                'answer' => "Copying prompts allows manual generation inside external AI tools. ImagineBuddy's automated studio allows you to upload a simple smartphone photo and instantly generate full catalog shots without manual prompt tweaking."
            ]
        ];
    }

    public function getFaqItemsAttribute(): array
    {
        if (!empty($this->faqs) && is_array($this->faqs) && count($this->faqs) > 0) {
            return $this->faqs;
        }

        return $this->getDefaultFaqs();
    }

    public function getRawImageUrlAttribute(): ?string
    {
        if (!empty($this->raw_image)) {
            $path = 'uploads/prompt-collections/before/' . $this->raw_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
            return asset('public/' . $path);
        }

        return null;
    }

    public function getTransformedImageUrlAttribute(): ?string
    {
        if (!empty($this->transformed_image)) {
            $path = 'uploads/prompt-collections/after/' . $this->transformed_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
            return asset('public/' . $path);
        }

        return null;
    }

    public function getDefaultShortcutFeatures(): array
    {
        return [
            'Try for free before choosing a plan',
            'No prompt engineering required',
            'Studio-quality in under 60 seconds',
            'Marketplace-ready exports',
        ];
    }

    public function getShortcutFeatureItemsAttribute(): array
    {
        if (!empty($this->shortcut_features) && is_array($this->shortcut_features) && count($this->shortcut_features) > 0) {
            return $this->shortcut_features;
        }

        return $this->getDefaultShortcutFeatures();
    }

    public function getDefaultMetricsItems(): array
    {
        return [
            [
                'stat' => '12x',
                'title' => 'Faster Campaign Turnaround',
                'description' => 'Produce entire editorial lookbooks in hours instead of four weeks of production planning',
            ],
            [
                'stat' => '90%',
                'title' => 'Production Cost Reduction',
                'description' => 'Eliminate model agency bookings, location permits, gear hire, and crew day rates',
            ],
            [
                'stat' => '3.8x',
                'title' => 'Higher Paid Social CTR',
                'description' => 'Driven by vibrant on-model editorial storytelling and high-frequency ad creative refreshes',
            ],
            [
                'stat' => '99.8%',
                'title' => 'Catalog Acceptance Rate',
                'description' => 'Zero image suppressions across Amazon Fashion, Myntra, ASOS, and Shopify channels',
            ],
        ];
    }

    public function getMetricsItemListAttribute(): array
    {
        if (!empty($this->metrics_items) && is_array($this->metrics_items) && count($this->metrics_items) > 0) {
            return $this->metrics_items;
        }

        return $this->getDefaultMetricsItems();
    }
}
