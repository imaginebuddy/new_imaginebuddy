<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsitePageSetting extends Model
{
    protected $table = 'website_page_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'extra_data' => 'array',
    ];

    /**
     * Default configurations dictionary
     */
    public static function getDefaults(string $key): array
    {
        $siteTitle = config('settings.title') ?: config('app.name', 'ImagineBuddy');

        $defaults = [
            'ai_models' => [
                'page_title' => 'AI Models',
                'subtitle' => 'Browse tested prompts by AI generator model',
                'seo_title' => 'AI Models - ' . $siteTitle,
                'meta_description' => 'Browse curated, tested AI prompts across Gemini, ChatGPT, Midjourney, Flux and more. Generate commercial product visuals on ' . $siteTitle . '.',
                'meta_keywords' => 'AI models, Gemini prompts, ChatGPT prompts, Midjourney prompts, Flux prompts, AI image prompts, product photography',
                'extra_data' => [
                    'search_placeholder' => 'Search AI models...',
                ],
            ],
            'photoshoots' => [
                'page_title' => 'Turn One Product Photo Into a Premium Multi Angle AI Photoshoot.',
                'subtitle' => 'Explore curated multi-prompt commercial photoshoot concepts. Generate consistent virtual models, studio lighting setups, and multiple camera angles across ChatGPT, Google Gemini, and Midjourney.',
                'seo_title' => 'AI Product Photoshoot Prompts & Multi-Angle Sets - ' . $siteTitle,
                'meta_description' => 'Explore curated multi-prompt commercial photoshoot concepts with consistent models, studio lighting setups, and multi-angle scenes.',
                'meta_keywords' => 'ai photoshoot, photoshoot prompts, product photography prompts, multi angle photoshoot, ecommerce photoshoot',
                'extra_data' => [
                    'all_categories_label' => 'All Categories',
                    'guide_badge' => 'Multi-Angle Prompt Consistency',
                    'guide_title' => 'Consistent Commercial AI Photoshoots for E-Commerce & Creators',
                    'guide_desc_1' => "Creating professional product catalogs and commercial campaigns with generative AI requires subject consistency across diverse camera angles, focal lengths, and studio lighting setups. ImagineBuddy's curated photoshoot sets provide tested, multi-prompt recipes that preserve subject identity, materials, and color accuracy across every frame.",
                    'guide_desc_2' => 'Create professional <strong>AI product photography</strong> for <a href="' . url('category/beauty-and-skincare') . '">beauty and skincare products</a>, <a href="' . url('category/footwear') . '">footwear</a>, <a href="' . url('category/fashion-and-apparel') . '">fashion and apparel</a>, <a href="' . url('category/electronics-and-gadgets') . '">consumer electronics</a>, and more. Our <strong>AI product photography prompts</strong> are designed for leading AI image generation platforms, including <strong>ChatGPT</strong>, <strong>Google Gemini</strong>, and <strong>Midjourney</strong>. Simply copy a prompt, past in your favorite ai image generator, and generate high-quality commercial product images, advertising visuals, and complete product lookbooks in minutes.',
                    'guide_card_title' => 'Why Use Photoshoot Sets?',
                    'guide_bullet_1_title' => 'Consistent Lighting & Ambience:',
                    'guide_bullet_1_desc' => 'Studio softbox, dramatic rim light, and natural golden hour consistency across every prompt.',
                    'guide_bullet_2_title' => 'Multi-Angle Coverage:',
                    'guide_bullet_2_desc' => 'Front hero view, 45° dynamic profile, macro texture close-ups, and contextual lifestyle shots.',
                    'guide_bullet_3_title' => 'Commercial License Ready:',
                    'guide_bullet_3_desc' => 'Tested prompt recipes tailored for e-commerce listings, social media ads, and brand marketing.',
                ],
            ],
            'prompts_free' => [
                'page_title' => 'Free Prompts',
                'subtitle' => 'Download free commercial product photography prompts tested across ChatGPT, Google Gemini, and Midjourney.',
                'seo_title' => 'Free AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Discover free AI product photography prompts for commercial e-commerce, packshots, and creative branding.',
                'meta_keywords' => 'free ai prompts, free product prompts, free midjourney prompts, free gemini prompts',
                'extra_data' => [],
            ],
            'prompts_premium' => [
                'page_title' => 'Premium Prompts',
                'subtitle' => 'Exclusive commercial product photography prompts crafted for high-converting brand campaigns and studio packshots.',
                'seo_title' => 'Premium AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Access premium commercial AI prompts with advanced lighting setups, camera lenses, and creative direction.',
                'meta_keywords' => 'premium ai prompts, pro product photography prompts, commercial ai prompts',
                'extra_data' => [],
            ],
            'popular' => [
                'page_title' => 'Popular Prompts',
                'subtitle' => 'Most popular commercial AI product photography prompts trending among creators and e-commerce brands.',
                'seo_title' => 'Popular AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Explore trending commercial AI prompts with proven conversion rates and top creator ratings.',
                'meta_keywords' => 'popular ai prompts, trending prompts, top product photography prompts',
                'extra_data' => [],
            ],
            'latest' => [
                'page_title' => 'Latest Prompts',
                'subtitle' => 'Fresh commercial AI product photography prompts added to our library every day.',
                'seo_title' => 'Latest AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Browse newly published commercial AI prompts with modern styling recipes and multi-angle concepts.',
                'meta_keywords' => 'latest ai prompts, new product prompts, recent midjourney prompts',
                'extra_data' => [],
            ],
            'most_viewed' => [
                'page_title' => 'Most Viewed Prompts',
                'subtitle' => 'Top-viewed commercial AI product photography prompts across all categories.',
                'seo_title' => 'Most Viewed AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Browse the most viewed commercial AI product photography prompts and creative inspirations.',
                'meta_keywords' => 'most viewed ai prompts, top viewed photography prompts',
                'extra_data' => [],
            ],
            'most_downloads' => [
                'page_title' => 'Most Downloaded Prompts',
                'subtitle' => 'Most downloaded commercial AI prompts trusted by e-commerce brands and agencies.',
                'seo_title' => 'Most Downloaded AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'See the most downloaded AI product photography prompts for studio packshots and marketing visuals.',
                'meta_keywords' => 'most downloaded ai prompts, best product prompts',
                'extra_data' => [],
            ],
            'most_copied' => [
                'page_title' => 'Most Copied Prompts',
                'subtitle' => 'Most copied prompt workflows ready to paste into ChatGPT, Google Gemini, and Midjourney.',
                'seo_title' => 'Most Copied AI Product Photography Prompts | ' . $siteTitle,
                'meta_description' => 'Access the most copied commercial AI prompts tested for instant execution across top AI models.',
                'meta_keywords' => 'most copied ai prompts, copy paste prompts, prompt recipes',
                'extra_data' => [],
            ],
            'pricing' => [
                'page_title' => 'Studio-Grade AI Photoshoots <br class="d-none d-md-block"><span class="text-color-default font-serif-italic">For Less Than a Cup of Coffee</span>',
                'subtitle' => 'Unlock 100 daily production-grade AI prompts with camera lenses, studio lighting recipes, multi-angle variations, and full commercial client rights.',
                'seo_title' => 'Pricing Plans - Studio AI Product Photography | ' . $siteTitle,
                'meta_description' => 'Affordable pricing plans for creators, e-commerce sellers, and agencies. Access daily production-grade AI prompts and commercial licenses.',
                'meta_keywords' => 'pricing, subscription plans, commercial ai prompts pricing, imaginebuddy plans',
                'extra_data' => [
                    'hero_badge_text' => 'Trusted by Creators, Ecom Brands & Agencies',
                    'save_badge_text' => 'Save up to 40%',
                    'table_heading' => 'Compare Plans & Feature Matrix',
                    'cta_title' => 'Ready to Elevate Your Commercial Visuals?',
                    'cta_subtitle' => 'Join thousands of top e-commerce sellers, agencies, and creators.',
                    'cta_btn_text' => 'Get Started Now',
                    'cta_guarantee' => 'Cancel anytime with one click. No questions asked.',
                ],
            ],
        ];

        return $defaults[$key] ?? [
            'page_title' => '',
            'subtitle' => '',
            'seo_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'extra_data' => [],
        ];
    }

    /**
     * Get settings for a page key with caching and fallback
     */
    public static function getByKey(string $key): self
    {
        return Cache::remember("website_page_settings_{$key}", 86400, function () use ($key) {
            $record = self::where('page_key', $key)->first();
            $defaults = self::getDefaults($key);

            if (!$record) {
                $record = new self(array_merge(['page_key' => $key], $defaults));
            } else {
                // Ensure empty fields fallback gracefully
                foreach (['page_title', 'subtitle', 'seo_title', 'meta_description', 'meta_keywords'] as $field) {
                    if (empty($record->{$field}) && !empty($defaults[$field])) {
                        $record->{$field} = $defaults[$field];
                    }
                }
                if (empty($record->extra_data) && !empty($defaults['extra_data'])) {
                    $record->extra_data = $defaults['extra_data'];
                }
            }

            return $record;
        });
    }

    /**
     * Helper to safely get extra_data item with fallback
     */
    public function getExtra(string $subKey, $default = '')
    {
        if (is_array($this->extra_data) && isset($this->extra_data[$subKey]) && $this->extra_data[$subKey] !== '') {
            return $this->extra_data[$subKey];
        }

        $defaults = self::getDefaults($this->page_key ?? '');
        return $defaults['extra_data'][$subKey] ?? $default;
    }

    /**
     * Clear cache on save/delete
     */
    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("website_page_settings_{$model->page_key}");
        });

        static::deleted(function ($model) {
            Cache::forget("website_page_settings_{$model->page_key}");
        });
    }
}
