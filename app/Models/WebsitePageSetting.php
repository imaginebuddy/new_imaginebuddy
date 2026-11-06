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
            'marketplace' => [
                'page_title' => 'Marketplace-Ready <span class="cms-heading-gradient-mint">Product Photos</span>',
                'subtitle' => 'Automate strict marketplace compliance: pure white RGB 255 backgrounds, 85%+ frame fill, high-res zoom, and zero catalog suppressions.',
                'seo_title' => 'Marketplace Compliance Product Photography - ' . $siteTitle,
                'meta_description' => 'Guaranteed compliance imagery for Amazon, Ajio, Blinkit, Etsy, Myntra, and Meta Shop. Avoid suppressions with instant AI generation.',
                'meta_keywords' => 'marketplace product photos, amazon product photography, ajio photography, blinkit compliance, etsy product photos, ecommerce packshots, white background ai',
                'extra_data' => [
                    'hero_badge' => 'COMPLIANCE GUARANTEED',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Marketplace Listings',
                    'grid_subheading' => 'Browse specialized marketplace workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
            'use_case' => [
                'page_title' => 'Transform Your <span class="cms-heading-gradient-mint">Product Imagery</span>',
                'subtitle' => "Discover how Imagine Buddy's AI-powered tools format, optimize, and customize your product photos for e-commerce platforms, social media, and beyond.",
                'seo_title' => 'AI Product Photography Use Cases - ' . $siteTitle,
                'meta_description' => 'Explore AI product photography use cases for D2C brands, bulk e-commerce cataloging, boutique shoots, background removal, and campaign visuals.',
                'meta_keywords' => 'ai product photography use cases, ecommerce product visuals, background removal ai, lifestyle photoshoot ai, d2c brand photoshoots',
                'extra_data' => [
                    'hero_badge' => 'ALL OUR SERVICES AT YOUR FINGERTIPS',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Use Cases',
                    'grid_subheading' => 'Browse specialized use case workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
            'industry' => [
                'page_title' => 'Industry-Specific <span class="cms-heading-gradient-mint">Photography Solutions</span>',
                'subtitle' => 'Tailored AI product photography workflows built to conquer the unique challenges of fashion, jewelry, cosmetics, electronics, and home decor.',
                'seo_title' => 'Industry-Specific AI Photography Solutions - ' . $siteTitle,
                'meta_description' => 'Professional AI photography tailored for e-commerce fashion, luxury jewelry, beauty products, electronics, and home decor brands.',
                'meta_keywords' => 'industry photography, jewelry ai photography, cosmetic product shoot, electronics packshot, fashion lookbook ai, home decor product images',
                'extra_data' => [
                    'hero_badge' => 'SOLUTIONS FOR EVERY SECTOR',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Industries',
                    'grid_subheading' => 'Browse specialized industry workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
            'product' => [
                'page_title' => 'Product Photos for <span class="cms-heading-gradient-mint">Every Purpose</span>',
                'subtitle' => "Whether you need carousel ads, social media posts, website hero imagery, or high-converting static creatives, we've got you covered.",
                'seo_title' => 'Product Photos & Ad Deliverables - ' . $siteTitle,
                'meta_description' => 'Generate carousel ad images, social media packs, static Facebook creatives, and website product photos instantly with AI.',
                'meta_keywords' => 'product photography assets, carousel ad photos, social media product images, website hero product photos, static ad creative ai',
                'extra_data' => [
                    'hero_badge' => 'FORMAT-OPTIMIZED ASSETS',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Product Deliverables',
                    'grid_subheading' => 'Browse specialized product content workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
            'garment' => [
                'page_title' => 'On-Model Apparel <span class="cms-heading-gradient-mint">Photography</span>',
                'subtitle' => 'Turn flat lays and ghost mannequins into studio-quality on-model fashion shoots. Perfect fabric texture, realistic drapes, and ethnic diversity.',
                'seo_title' => 'AI Apparel & Garment Photography - ' . $siteTitle,
                'meta_description' => 'AI fashion photography for sarees, kurtas, dresses, western wear, and streetwear. True-to-fabric textures and diverse virtual models.',
                'meta_keywords' => 'ai garment photography, on-model fashion shoot, saree ai photography, ghost mannequin to model, apparel lookbook ai, fashion prompts',
                'extra_data' => [
                    'hero_badge' => 'FABRIC & DRAPE FIDELITY',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Garments & Apparel',
                    'grid_subheading' => 'Browse specialized garment workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
            'platform_ai_ads' => [
                'page_title' => 'High-Converting <span class="cms-heading-gradient-mint">Platform Ad Creatives</span>',
                'subtitle' => 'Generate scroll-stopping ad creatives formatted for Facebook, Instagram Reels, Google Display, TikTok, Pinterest, and YouTube.',
                'seo_title' => 'Platform AI Ad Creatives & Video Formats - ' . $siteTitle,
                'meta_description' => 'High-converting AI ad visuals and story/feed creatives for Meta Ads, Google Display, TikTok, and Pinterest marketing campaigns.',
                'meta_keywords' => 'platform ai ads, facebook ad creatives, instagram story ads, google display ads, tiktok visual prompts, high ctr ecommerce ads',
                'extra_data' => [
                    'hero_badge' => 'ROAS & CTR OPTIMIZATION',
                    'hero_cta_text' => 'Explore Solutions',
                    'hero_cta_url' => '#catalog',
                    'grid_badge' => 'CATALOG',
                    'grid_heading' => 'All Ad Creatives',
                    'grid_subheading' => 'Browse specialized platform ad creative workflows and production-ready photography guides.',
                    'social_proof_badge' => 'TRUSTED BY 500+ D2C BRANDS & AGENCIES',
                    'cta_badge' => 'GET STARTED',
                    'cta_heading' => 'Scale Your Product Visuals with AI',
                    'cta_subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
                    'cta_btn_text' => 'Create Free Account',
                    'cta_btn_url' => url('register'),
                    'cta_secondary_btn_text' => 'View Pricing Plans',
                    'cta_secondary_btn_url' => url('pricing'),
                ],
            ],
        ];

        $normalizedKey = str_replace('-', '_', $key);
        return $defaults[$normalizedKey] ?? [
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
        $normalizedKey = str_replace('-', '_', $key);
        return Cache::remember("website_page_settings_{$normalizedKey}", 86400, function () use ($normalizedKey) {
            $record = self::where('page_key', $normalizedKey)->first();
            $defaults = self::getDefaults($normalizedKey);

            if (!$record) {
                $record = new self(array_merge(['page_key' => $normalizedKey], $defaults));
            } else {
                // Ensure empty fields fallback gracefully
                foreach (['page_title', 'subtitle', 'seo_title', 'meta_description', 'meta_keywords'] as $field) {
                    if (empty($record->{$field}) && !empty($defaults[$field])) {
                        $record->{$field} = $defaults[$field];
                    }
                }
                $existingExtra = is_array($record->extra_data) ? $record->extra_data : [];
                $defaultExtra = is_array($defaults['extra_data'] ?? null) ? $defaults['extra_data'] : [];
                $record->extra_data = array_merge($defaultExtra, $existingExtra);
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
            Cache::forget("website_page_settings_" . str_replace('_', '-', $model->page_key));
        });

        static::deleted(function ($model) {
            Cache::forget("website_page_settings_{$model->page_key}");
            Cache::forget("website_page_settings_" . str_replace('_', '-', $model->page_key));
        });
    }
}
