<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SeoMetadata;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        // 1. Insert FAQ SEO Metadata if not existing
        $exists = DB::table('seo_metadata')->where('page_key', 'faq')->exists();
        if (!$exists) {
            DB::table('seo_metadata')->insert([
                'page_key' => 'faq',
                'page_name' => 'Frequently Asked Questions',
                'route_name' => 'faq',
                'path' => '/frequently-asked-questions',
                'type' => 'static_page',
                'meta_title' => 'Frequently Asked Questions - ImagineBuddy',
                'meta_description' => 'Find answers to frequently asked questions about ImagineBuddy AI prompts, commercial licensing, photoshoots, subscriptions, and downloads.',
                'meta_keywords' => 'FAQ, frequently asked questions, AI prompt questions, licensing FAQ, imaginebuddy help',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'og_title' => 'Frequently Asked Questions - ImagineBuddy',
                'og_description' => 'Find answers to frequently asked questions about ImagineBuddy AI prompts, commercial licensing, photoshoots, subscriptions, and downloads.',
                'og_image' => null,
                'twitter_card' => 'summary_large_image',
                'schema_type' => 'FAQPage',
                'schema_custom' => null,
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Cache::forget(SeoMetadata::CACHE_KEY);
        }

        // 2. Seed default FAQ records if table is empty
        if (DB::table('faqs')->count() === 0) {
            $initialFaqs = [
                [
                    'question' => 'How does the AI Product Photoshoot Prompt Library work?',
                    'answer' => '<p>Our library provides production-grade, battle-tested prompt recipes specifically engineered for commercial product photography. <strong>No manual prompt editing is required.</strong> Simply use the prompt with your product image, and the intelligent prompt framework analyzes your product and automatically adapts the visual direction including colors, background, theme, props, composition, lighting, textures, and overall styling to create a cohesive, premium product photoshoot tailored to your product. Run it in Midjourney, Gemini, ChatGPT, or your preferred AI image generator and generate studio-grade commercial visuals in minutes.</p>',
                    'category' => 'Photoshoots & Prompts',
                    'sort_order' => 1,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'question' => 'What AI image generators are these prompts compatible with?',
                    'answer' => '<p>Every prompt in our library is tested and optimized across leading AI models, including Midjourney (v6+), Google Gemini / Imagen 3, ChatGPT / DALL·E 3, and Stable Diffusion. Each prompt entry includes recommended aspect ratios, stylize parameters, and lighting settings for seamless, artifact-free generation.</p>',
                    'category' => 'Compatibility',
                    'sort_order' => 2,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'question' => 'Can I use the generated images for commercial ecommerce and ads?',
                    'answer' => '<p>Yes, absolutely. All premium photoshoot prompts in our library come with full commercial rights. You can freely use the resulting visual concepts and imagery across your ecommerce stores (Shopify, Amazon), digital advertising campaigns, social media channels, brand presentations, and marketing collateral without royalties.</p>',
                    'category' => 'Licensing & Rights',
                    'sort_order' => 3,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'question' => 'How is this different from organizing a traditional product photoshoot?',
                    'answer' => '<p>A traditional photoshoot requires booking a studio, hiring photographers, sourcing physical props, coordinating models, shipping product samples, and waiting weeks for retouching rounds—often costing $500 to $2,500+ (₹35,000 to ₹75,000+) every time you launch a new SKU. With Imagine Buddy, you get unlimited access to 100+ commercial photoshoot styles for just $3/month (or ₹250/month in India), enabling instant creative ideation on demand.</p>',
                    'category' => 'General',
                    'sort_order' => 4,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'question' => 'Do I have to pay per prompt, or is access unlimited?',
                    'answer' => '<p>Access is 100% unlimited. With your active membership, you never pay per prompt or per concept. You can browse, unlock, and copy as many premium photoshoot prompts as you need across all product categories (beverages, cosmetics, food, packaging, lifestyle) without credit deductions or hidden fees.</p>',
                    'category' => 'Pricing & Plans',
                    'sort_order' => 5,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'question' => 'Can you match our existing brand guidelines and color palettes?',
                    'answer' => '<p>Yes. Our prompts are structured with modular variables so you can effortlessly customize lighting setups (warm golden hour, high-key studio softbox, dramatic rim lighting), backdrops (sandstone pedestals, luxury marble, natural water ripples), and color accents to strictly align with your brand guidelines and aesthetic identity.</p>',
                    'category' => 'Customization',
                    'sort_order' => 6,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];

            DB::table('faqs')->insert($initialFaqs);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('seo_metadata')->where('page_key', 'faq')->delete();
        Cache::forget(SeoMetadata::CACHE_KEY);
        DB::table('faqs')->truncate();
    }
};
