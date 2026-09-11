<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('seo_metadata')) {
            Schema::create('seo_metadata', function (Blueprint $table) {
                $table->id();
                $table->string('page_key', 60)->unique();
                $table->string('page_name', 100);
                $table->string('route_name', 100)->nullable();
                $table->string('path', 255)->nullable();
                $table->enum('type', ['static_page', 'dynamic_template'])->default('static_page');
                $table->string('meta_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('canonical_url', 255)->nullable();
                $table->string('robots', 50)->default('index, follow');
                $table->string('og_title', 255)->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image', 255)->nullable();
                $table->string('twitter_card', 30)->default('summary_large_image');
                $table->string('schema_type', 50)->nullable();
                $table->text('schema_custom')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->string('seoable_type', 100)->nullable();
                $table->unsignedBigInteger('seoable_id')->nullable();
                $table->timestamps();

                $table->index(['page_key', 'is_active']);
                $table->index(['path', 'is_active']);
                $table->index('type');
                $table->index(['seoable_type', 'seoable_id']);
            });

            // Seed default static pages and pattern templates
            $this->seedDefaults();
        }
    }

    /**
     * Seed initial 13 pages and 2 dynamic pattern templates.
     */
    protected function seedDefaults(): void
    {
        $now = now();
        $pages = [
            [
                'page_key' => 'home',
                'page_name' => 'Homepage',
                'route_name' => 'home',
                'path' => '/',
                'type' => 'static_page',
                'meta_title' => 'ImagineBuddy - AI Image Prompts, Generators & Creative Stock',
                'meta_description' => 'Discover thousands of curated AI image prompts, creative stock photos, and prompt engineering assets. Copy prompts and generate stunning AI art instantly.',
                'meta_keywords' => 'AI prompts, Midjourney prompts, DALL-E prompts, Stable Diffusion, prompt engineering, AI stock photos, AI art',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'WebSite',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'pricing',
                'page_name' => 'Pricing & Plans',
                'route_name' => 'pricing',
                'path' => '/pricing',
                'type' => 'static_page',
                'meta_title' => 'Pricing Plans & Credit Packages - ImagineBuddy',
                'meta_description' => 'Choose the perfect subscription plan or credit package for high-resolution AI downloads, commercial licenses, and creative assets.',
                'meta_keywords' => 'pricing, subscription, AI credits, commercial license, buy stock photos, image subscription',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'PricingPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'photoshoots_index',
                'page_name' => 'Photoshoots',
                'route_name' => 'photoshoots',
                'path' => '/photoshoots',
                'type' => 'static_page',
                'meta_title' => 'AI Photoshoots & Thematic Collections - ImagineBuddy',
                'meta_description' => 'Explore curated AI photoshoot sessions with consistent models, styles, and prompt recipes for professional creative projects.',
                'meta_keywords' => 'AI photoshoots, consistent AI characters, photoshoot prompts, photography styles, virtual models',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'photos_premium',
                'page_name' => 'Premium Photos',
                'route_name' => 'photos_premium',
                'path' => '/photos/premium',
                'type' => 'static_page',
                'meta_title' => 'Premium AI Prompts & Exclusive Photos - ImagineBuddy',
                'meta_description' => 'Browse exclusive, high-end AI prompts and premium commercial photos generated by expert prompt engineers.',
                'meta_keywords' => 'premium AI photos, exclusive prompts, commercial AI art, high resolution AI stock',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_featured',
                'page_name' => 'Featured Photos',
                'route_name' => 'featured',
                'path' => '/featured',
                'type' => 'static_page',
                'meta_title' => 'Featured AI Prompts & Artwork - ImagineBuddy',
                'meta_description' => 'Handpicked collection of top-performing AI prompts and trending digital creations featured by our curation team.',
                'meta_keywords' => 'featured prompts, staff picks, trending AI art, best AI images, prompt inspiration',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_popular',
                'page_name' => 'Popular Photos',
                'route_name' => 'popular',
                'path' => '/popular',
                'type' => 'static_page',
                'meta_title' => 'Most Popular AI Prompts & Images - ImagineBuddy',
                'meta_description' => 'See the most popular AI image prompts liked and saved by creators worldwide.',
                'meta_keywords' => 'popular AI prompts, trending prompts, most liked AI art, top community prompts',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_latest',
                'page_name' => 'Latest Photos',
                'route_name' => 'latest',
                'path' => '/latest',
                'type' => 'static_page',
                'meta_title' => 'Latest AI Prompts & Fresh Uploads - ImagineBuddy',
                'meta_description' => 'Discover the newest AI prompts, recent uploads, and modern creative inspiration added daily.',
                'meta_keywords' => 'new AI prompts, latest prompts, fresh AI images, recently added prompts',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_viewed',
                'page_name' => 'Most Viewed Photos',
                'route_name' => 'most_viewed',
                'path' => '/most/viewed',
                'type' => 'static_page',
                'meta_title' => 'Most Viewed AI Prompts & Photos - ImagineBuddy',
                'meta_description' => 'Explore the highest-trafficked and most-viewed prompt collections across the platform.',
                'meta_keywords' => 'most viewed prompts, viral AI images, high engagement prompts, popular AI art',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_downloads',
                'page_name' => 'Most Downloaded Photos',
                'route_name' => 'most_downloads',
                'path' => '/most/downloads',
                'type' => 'static_page',
                'meta_title' => 'Most Downloaded AI Assets & Prompts - ImagineBuddy',
                'meta_description' => 'The community\'s top-downloaded creative stock photos, vector graphics, and tested prompt recipes.',
                'meta_keywords' => 'top downloaded stock, best prompt downloads, stock photo downloads, popular creative assets',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'explore_copied',
                'page_name' => 'Most Copied Prompts',
                'route_name' => 'most_copied',
                'path' => '/most/copied',
                'type' => 'static_page',
                'meta_title' => 'Most Copied AI Prompts & Recipes - ImagineBuddy',
                'meta_description' => 'Tested prompt recipes copied the most by artists, designers, and marketers for Midjourney, DALL-E, and Stable Diffusion.',
                'meta_keywords' => 'copy prompt, best prompt recipes, copy AI prompt, top copied prompts, prompt formulas',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'page_terms',
                'page_name' => 'Terms & Conditions',
                'route_name' => 'page.terms',
                'path' => '/page/terms-and-conditions',
                'type' => 'static_page',
                'meta_title' => 'Terms of Service & Licensing Agreement - ImagineBuddy',
                'meta_description' => 'Review our terms of service, commercial licensing rights, acceptable use policy, and user agreement.',
                'meta_keywords' => 'terms of service, licensing agreement, user terms, legal policy, copyright',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'WebPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'page_privacy',
                'page_name' => 'Privacy Policy',
                'route_name' => 'page.privacy',
                'path' => '/page/privacy-policy',
                'type' => 'static_page',
                'meta_title' => 'Privacy Policy & Data Protection - ImagineBuddy',
                'meta_description' => 'Learn how ImagineBuddy collects, protects, and manages your personal information and privacy rights.',
                'meta_keywords' => 'privacy policy, data protection, GDPR compliance, personal data, privacy rights',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'WebPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'contact',
                'page_name' => 'Contact Us',
                'route_name' => 'contact',
                'path' => '/contact',
                'type' => 'static_page',
                'meta_title' => 'Contact Support & Creative Inquiries - ImagineBuddy',
                'meta_description' => 'Need assistance or have feedback? Contact our support team for customer service, account help, or partnerships.',
                'meta_keywords' => 'contact us, customer support, help desk, contact email, feedback',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'ContactPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Dynamic Pattern Templates
            [
                'page_key' => 'prompt_template',
                'page_name' => 'Default Prompt Detail Template',
                'route_name' => 'prompt.show',
                'path' => '/prompt/{slug}',
                'type' => 'dynamic_template',
                'meta_title' => '{title} - AI Prompt & Image | {site_name}',
                'meta_description' => 'Copy the prompt for "{title}" in {category}. Tested prompt recipe with tags, creative parameters, and variation ideas on {site_name}.',
                'meta_keywords' => '{tags}, {category}, AI prompt, Midjourney prompt',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'ImageObject',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'category_template',
                'page_name' => 'Default Category Page Template',
                'route_name' => 'category',
                'path' => '/category/{slug}',
                'type' => 'dynamic_template',
                'meta_title' => 'Best {category} AI Prompts & Images | {site_name}',
                'meta_description' => 'Discover and copy top {category} prompts. Download free and premium high-resolution AI art and prompt engineering inspiration on {site_name}.',
                'meta_keywords' => '{category} prompts, {category} AI art, prompt engineering',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('seo_metadata')->insert($pages);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
    }
};
