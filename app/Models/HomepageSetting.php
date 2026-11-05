<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomepageSetting extends Model
{
    protected $table = 'homepage_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'diff_enabled' => 'boolean',
        'diff_card_generic_items' => 'array',
        'diff_card_adaptive_items' => 'array',
        'photoshoots_enabled' => 'boolean',
        'how_it_works_enabled' => 'boolean',
        'how_it_works_steps' => 'array',
        'who_is_it_for_enabled' => 'boolean',
        'who_is_it_for_items' => 'array',
        'free_vs_pro_enabled' => 'boolean',
        'promo_section_enabled' => 'boolean',
        'promo_items' => 'array',
        'transformation_enabled' => 'boolean',
        'transformation_traditional_items' => 'array',
        'transformation_library_items' => 'array',
        'faq_enabled' => 'boolean',
        'faq_items' => 'array',
    ];

    /**
     * Cache key for homepage settings singleton.
     */
    public const CACHE_KEY = 'homepage_settings_data';

    /**
     * Default static values matching the current homepage.
     */
    public static function defaultValues(): array
    {
        return [
            // 1. Hero Section
            'hero_title' => 'Adaptive AI Product Photography Prompts Library',
            'hero_subtitle' => 'Turn a simple product photo into studio, lifestyle, and campaign visuals using adaptive prompts designed for ChatGPT and Gemini. No complicated prompt writing required.',
            'search_placeholder' => 'Search prompt, photoshoot, brand...',

            // 2. What Makes ImagineBuddy Different?
            'diff_enabled' => true,
            'diff_eyebrow' => 'WHAT MAKES IMAGINEBUDDY DIFFERENT?',
            'diff_heading' => 'One Photography Style. <span class="font-serif-italic">Adapted to</span> Your Product.',
            'diff_description_1' => "Generic prompts often need manual adjustments for different products. ImagineBuddy's adaptive prompts are designed to guide AI to create product-focused visuals based on the product you upload.",
            'diff_description_2' => 'Whether it\'s skincare, jewellery, fashion, food, or home d&eacute;cor, explore creative photography concepts tailored to your product.',
            'diff_card_generic_title' => 'Generic Prompts',
            'diff_card_generic_items' => [
                'Often require manual edits for different products.',
                'May need adjustments to colours, props, backgrounds, and lighting.',
                'Can require repeated experimentation to achieve the desired look.',
            ],
            'diff_card_adaptive_title' => 'Adaptive Prompts by ImagineBuddy',
            'diff_card_adaptive_items' => [
                'Designed to respond to the uploaded product.',
                'Guide product-focused styling, lighting, props, and backgrounds.',
                'Help you explore different creative directions with less manual prompt editing.',
            ],
            'diff_cta_text' => 'Explore Adaptive Prompts',
            'diff_cta_url' => 'latest',
            'diff_disclaimer' => 'Results depend on the AI image generator, the prompt, and the uploaded product image.',

            // 3. Latest Photoshoots
            'photoshoots_enabled' => true,
            'photoshoots_heading' => 'Latest Photoshoots',
            'photoshoots_subheading' => 'Explore curated multi-angle photoshoot sets and commercial prompt batches designed for product launches, ad campaigns, and catalog consistency.',
            'photoshoots_cta_text' => 'View all',
            'photoshoots_cta_url' => 'photoshoots',

            // 4. How It Works
            'how_it_works_enabled' => true,
            'how_it_works_eyebrow' => 'YOUR CREATIVE WORKFLOW, SIMPLIFIED',
            'how_it_works_heading' => 'From Product Photo to <span class="font-serif-italic">AI Photoshoot</span> in 3 Steps',
            'how_it_works_steps' => [
                [
                    'badge' => 'STEP 1',
                    'icon' => 'bi-upload',
                    'title' => 'Upload Your Product',
                    'desc' => 'Choose a clear photo of your product. It can be skincare, a handbag, jewellery, food, electronics, or another product.',
                ],
                [
                    'badge' => 'STEP 2',
                    'icon' => 'bi-copy',
                    'title' => 'Copy an Adaptive Prompt',
                    'desc' => "Explore ImagineBuddy's photography concepts and copy a prompt that matches the style you want to create.",
                ],
                [
                    'badge' => 'STEP 3',
                    'icon' => 'bi-stars',
                    'title' => 'Generate Your Visual',
                    'desc' => 'Upload your product to ChatGPT or Gemini, paste the prompt, and generate a new product photography concept.',
                ],
            ],
            'how_it_works_cta_text' => 'Try Your First Prompt',
            'how_it_works_cta_url' => 'prompts/free',
            'how_it_works_subtext' => 'No need to start every creative idea from a blank prompt.',

            // 5. Who Is ImagineBuddy For
            'who_is_it_for_enabled' => true,
            'who_is_it_for_heading' => 'Built for People Who Need Better Product Visuals',
            'who_is_it_for_subheading' => 'Whether you run a brand or create marketing content, ImagineBuddy helps you explore new directions for product photography.',
            'who_is_it_for_items' => [
                [
                    'title' => 'E-commerce Business Owners',
                    'desc' => 'Develop more visual concepts for your product catalogue.',
                ],
                [
                    'title' => 'Brand Owners',
                    'desc' => 'Explore fresh campaign ideas without planning every concept from scratch.',
                ],
                [
                    'title' => 'Digital Marketers',
                    'desc' => 'Create starting points for advertising and social media creatives.',
                ],
                [
                    'title' => 'Designers & Creative Agencies',
                    'desc' => 'Experiment with product-focused visual directions.',
                ],
                [
                    'title' => 'Photographers',
                    'desc' => 'Use AI to explore moodboards, compositions, and creative references.',
                ],
                [
                    'title' => 'Content Creators',
                    'desc' => 'Develop product visuals for marketing content and brand collaborations.',
                ],
            ],
            'who_is_it_for_cta_text' => 'Discover What You Can Create',
            'who_is_it_for_cta_url' => 'latest',

            // 6. Free vs Pro
            'free_vs_pro_enabled' => true,
            'free_pro_eyebrow' => 'START CREATING, THEN EXPLORE MORE',
            'free_pro_heading' => 'Find Your Next Product Photography Idea',
            'free_pro_subheading' => 'Not sure which style suits your product? Start with the free prompts, explore the results, and discover the creative possibilities of adaptive AI product photography.',
            'free_card_title' => 'Start Free',
            'free_card_desc' => 'Explore available free prompts and experiment with product photography concepts using a supported AI image generator.',
            'free_card_cta_text' => 'Explore Free Prompts',
            'free_card_cta_url' => 'prompts/free',
            'pro_card_title' => 'Explore Pro',
            'pro_card_desc' => 'Unlock the additional prompt access and creative options included in the Pro plan.',
            'pro_card_cta_text' => 'View Plans & Pricing',
            'pro_card_cta_url' => 'pricing',
            'free_pro_footnote' => 'Check the pricing page for the current plan details and included features.',

            // 7. Promo Section
            'promo_section_enabled' => true,
            'promo_eyebrow' => 'ONE PRODUCT, MORE CREATIVE POSSIBILITIES',
            'promo_heading' => 'More Ways to Showcase Your Product',
            'promo_subheading' => 'Your product may need more than one image. Explore different photography concepts for different marketing goals.',
            'promo_items' => [
                [
                    'title' => 'E-commerce Product Images:',
                    'desc' => 'Clean, product-focused visuals for online stores.',
                ],
                [
                    'title' => 'Premium Studio Photography:',
                    'desc' => 'Polished compositions with professional-looking lighting.',
                ],
                [
                    'title' => 'Lifestyle Product Photography:',
                    'desc' => 'Place products in visually engaging environments.',
                ],
                [
                    'title' => 'Social Media Creatives:',
                    'desc' => 'Develop fresh concepts for Instagram and other social platforms.',
                ],
                [
                    'title' => 'Advertising Visuals:',
                    'desc' => 'Explore campaign-style compositions for marketing creatives.',
                ],
                [
                    'title' => 'Seasonal Campaign Concepts:',
                    'desc' => 'Experiment with festive, promotional, and occasion-based styling.',
                ],
            ],
            'promo_note' => 'Use these concepts as starting points and review every generated image for product accuracy, readable branding, and realistic details.',
            'promo_cta_text' => 'Find Your Photography Style',
            'promo_cta_url' => 'categories',

            // 8. Transformation Showcase
            'transformation_enabled' => true,
            'transformation_eyebrow' => 'THE TRANSFORMATION',
            'transformation_heading' => 'See the <span class="font-serif-italic">Difference Yourself</span>',
            'transformation_subheading' => 'Witness how simple, unedited product snapshots are instantly elevated into highly polished, premium brand campaigns.',
            'transformation_before_image' => 'public/img/White-Oud-Perfume-01.webp',
            'transformation_after_image' => 'public/img/premium-white-oud-perfume-ad-02.webp',
            'transformation_before_label' => 'Raw Snapshot',
            'transformation_after_label' => 'Campaign Ready',
            'transformation_right_badge' => 'AI Photoshoot Prompt Library',
            'transformation_right_badge_sub' => 'Unlimited Creative Ideation',
            'transformation_right_headline' => 'Studio-Quality Product Concepts. <br class="d-none d-sm-inline"><span class="text-mint">Without the Photoshoot Overhead.</span>',
            'transformation_right_description' => 'Create premium product photography concepts without the cost, logistics, and delays of traditional photoshoots. Access battle-tested AI prompt recipes tailored for ecommerce, ads, and product launches—generating new visual directions on demand without booking a studio for every new campaign or SKU.',
            'transformation_traditional_items' => [
                'Photographer & studio rental',
                'Props, models & set styling',
                'Shipping logistics & days of delay',
                'Repeated cost for every single SKU',
            ],
            'transformation_traditional_price_inr' => '₹35,000 – ₹75,000+ / shoot',
            'transformation_traditional_price_usd' => '$500 – $2,500+ / shoot',
            'transformation_library_items' => [
                'Unlimited prompt access',
                'Multi-angle & commercial aesthetics',
                'Instant concept iterations in minutes',
                'Zero studio bookings or physical logistics',
            ],
            'transformation_library_price_inr' => '₹250 /month',
            'transformation_library_price_usd' => '$3 /month',
            'transformation_cta_title' => 'Never Pay Per Prompt',
            'transformation_cta_subtitle' => 'Unlock the full commercial photoshoot prompt library for just standard subscription.',
            'transformation_cta_btn_text' => 'Unlock Full Prompt Library',
            'transformation_cta_btn_url' => 'pricing',

            // 9. FAQ Section
            'faq_enabled' => true,
            'faq_badge' => 'FAQ',
            'faq_title' => 'Frequently Asked Questions',
            'faq_subtitle' => 'Everything you need to know about our photoshoot prompt library before getting started.',
            'faq_contact_text' => 'Can’t find your answer? Just ask us.',
            'faq_contact_url' => 'contact',
            'faq_view_all_text' => 'Browse All FAQs',
            'faq_view_all_url' => 'frequently-asked-questions',
            'faq_items' => [
                [
                    'question' => 'What is ImagineBuddy?',
                    'answer' => '<p>ImagineBuddy is an AI product photography prompt platform that helps users explore product-focused visual concepts using AI image generators such as ChatGPT and Gemini.</p>',
                ],
                [
                    'question' => 'What are adaptive product photography prompts?',
                    'answer' => '<p>Adaptive prompts are designed to guide AI image generation according to the uploaded product. Depending on the prompt and image generator, the product’s appearance can influence the styling, background, props, lighting, and overall creative direction.</p>',
                ],
                [
                    'question' => 'How do I use an ImagineBuddy prompt?',
                    'answer' => '<p>Choose a prompt on ImagineBuddy, copy it, upload your product photo to a compatible AI image generator, paste the prompt, and generate your visual.</p>',
                ],
                [
                    'question' => 'Can I use ImagineBuddy for any product?',
                    'answer' => '<p>ImagineBuddy’s product photography concepts can be explored across many categories, including skincare, jewellery, fashion, food, electronics, furniture, and more. Results will vary depending on the product, prompt, and AI tool.</p>',
                ],
                [
                    'question' => 'Do I need professional photography equipment?',
                    'answer' => '<p>You can start with a clear product photo and a compatible AI image generator. You do not need to arrange a traditional studio setup to experiment with AI-generated concepts.</p>',
                ],
                [
                    'question' => 'Does ImagineBuddy generate the images?',
                    'answer' => '<p>ImagineBuddy provides product photography prompts. You use those prompts with a compatible AI image generator to create your visuals.</p>',
                ],
                [
                    'question' => 'Can I use the visuals for commercial purposes?',
                    'answer' => '<p>Commercial usage rights depend on the AI image generator you use, its terms, the assets involved, and any applicable brand or intellectual property rights. Check the relevant terms before using generated visuals commercially.</p>',
                ],
                [
                    'question' => 'Are there free prompts?',
                    'answer' => '<p>Yes, explore the free prompts available on ImagineBuddy to get started. Visit the pricing page to compare the current free and Pro plans.</p>',
                ],
            ],
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
