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
        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedBigInteger('blog_category_id')->nullable();
                $table->string('title', 255);
                $table->string('slug', 255)->unique();
                $table->string('preview_token', 64)->unique();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->string('featured_image', 255)->nullable();
                $table->string('featured_image_alt', 255)->nullable();
                $table->enum('status', ['published', 'draft', 'scheduled'])->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->unsignedSmallInteger('reading_time')->default(1);
                $table->unsignedInteger('views_count')->default(0);
                $table->timestamp('published_at')->nullable();

                // SEO & Social Meta
                $table->string('meta_title', 255)->nullable();
                $table->string('meta_description', 500)->nullable();
                $table->string('meta_keywords', 255)->nullable();
                $table->string('canonical_url', 500)->nullable();
                $table->string('robots', 50)->default('index, follow');
                $table->string('og_title', 255)->nullable();
                $table->string('og_description', 500)->nullable();
                $table->string('og_image', 255)->nullable();
                $table->string('twitter_card', 50)->default('summary_large_image');
                $table->string('schema_type', 50)->default('BlogPosting');

                $table->timestamps();

                // Indexes & Constraints
                $table->index(['status', 'published_at']);
                $table->index(['blog_category_id', 'status']);
                $table->index('is_featured');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
