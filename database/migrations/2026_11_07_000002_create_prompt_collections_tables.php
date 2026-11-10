<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Settings table for /prompts hub page
        if (!Schema::hasTable('prompt_page_settings')) {
            Schema::create('prompt_page_settings', function (Blueprint $table) {
                $table->id();
                $table->string('hero_badge')->nullable();
                $table->string('hero_title')->nullable();
                $table->text('hero_subtitle')->nullable();
                $table->string('banner_title')->nullable();
                $table->text('banner_subtitle')->nullable();
                $table->string('banner_cta_text')->nullable();
                $table->string('banner_cta_url')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->timestamps();
            });

            // Seed default hub settings
            \DB::table('prompt_page_settings')->insert([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Prompt collections table
        if (!Schema::hasTable('prompt_collections')) {
            Schema::create('prompt_collections', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('title');
                $table->string('slug')->unique()->index();
                $table->text('summary')->nullable();
                $table->unsignedInteger('category_id')->nullable()->index();
                $table->string('cover_image')->nullable();
                $table->string('ai_engine')->default('Midjourney & Flux')->nullable();
                $table->longText('description')->nullable();
                $table->json('creative_guide')->nullable();
                $table->json('faqs')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->enum('status', ['active', 'draft'])->default('active')->index();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->unsignedBigInteger('views_count')->default(0);
                $table->timestamps();

                if (Schema::hasTable('categories')) {
                    $table->foreign('category_id')
                          ->references('id')
                          ->on('categories')
                          ->onDelete('set null');
                }
            });
        }

        // 3. Prompt collection items (Pivot to images / prompts)
        if (!Schema::hasTable('prompt_collection_items')) {
            Schema::create('prompt_collection_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('prompt_collection_id')->index();
                $table->integer('image_id')->index(); // matches images.id int(11) signed
                $table->string('custom_shot_type')->nullable();
                $table->text('custom_prompt_override')->nullable();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();

                $table->foreign('prompt_collection_id')
                      ->references('id')
                      ->on('prompt_collections')
                      ->onDelete('cascade');

                if (Schema::hasTable('images')) {
                    $table->foreign('image_id')
                          ->references('id')
                          ->on('images')
                          ->onDelete('cascade');
                }

                $table->unique(['prompt_collection_id', 'image_id'], 'p_coll_item_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_collection_items');
        Schema::dropIfExists('prompt_collections');
        Schema::dropIfExists('prompt_page_settings');
    }
};
