<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('seo_landing_pages')) {
            Schema::create('seo_landing_pages', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('category_id')->nullable();
                $table->string('internal_name', 255)->nullable();
                $table->string('title', 255);
                $table->string('slug', 255)->unique();
                $table->string('preview_token', 64)->unique();
                $table->text('excerpt')->nullable();
                $table->string('featured_image', 255)->nullable();
                $table->string('featured_image_alt', 255)->nullable();
                $table->string('target_audience', 255)->nullable();
                $table->string('primary_keyword', 150)->nullable();
                $table->text('secondary_keywords')->nullable();
                $table->enum('search_intent', ['informational', 'commercial', 'transactional'])->default('commercial');
                $table->enum('status', ['published', 'draft', 'scheduled', 'archived'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->timestamp('scheduled_for')->nullable();

                // SEO & Social Meta
                $table->string('meta_title', 255)->nullable();
                $table->string('meta_description', 500)->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('canonical_url', 500)->nullable();
                $table->string('robots', 50)->default('index, follow');
                $table->string('og_title', 255)->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image', 255)->nullable();
                $table->string('twitter_card', 50)->default('summary_large_image');
                $table->string('schema_type', 50)->default('Article');

                // Page Configuration Toggles
                $table->boolean('enable_table_of_contents')->default(true);
                $table->boolean('enable_prompt_copying')->default(true);
                $table->unsignedBigInteger('views_count')->default(0);

                $table->timestamps();

                // Indexes & Constraints
                $table->index(['status', 'published_at']);
                $table->index('primary_keyword');
                $table->index('category_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_landing_pages');
    }
};
