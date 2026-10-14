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
        if (!Schema::hasTable('seo_landing_page_sections')) {
            Schema::create('seo_landing_page_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('landing_page_id');
                $table->enum('type', [
                    'hero',
                    'rich_text',
                    'prompt_grid',
                    'photoshoot_showcase',
                    'image_gallery',
                    'camera_guide',
                    'product_adaptability',
                    'faq_accordion',
                    'cta_banner'
                ])->default('rich_text');
                $table->string('badge', 100)->nullable();
                $table->string('heading', 255)->nullable();
                $table->string('subheading', 255)->nullable();
                $table->mediumText('content')->nullable();
                $table->json('settings')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                // Indexes & Constraints
                $table->index(['landing_page_id', 'sort_order']);
                $table->index('is_active');
                $table->foreign('landing_page_id')->references('id')->on('seo_landing_pages')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_landing_page_sections');
    }
};
