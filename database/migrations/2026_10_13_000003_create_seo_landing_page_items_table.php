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
        if (!Schema::hasTable('seo_landing_page_items')) {
            Schema::create('seo_landing_page_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('landing_page_id');
                $table->unsignedBigInteger('section_id')->nullable();
                $table->enum('item_type', ['prompt', 'photoshoot', 'custom_image'])->default('prompt');
                $table->unsignedBigInteger('item_id'); // References images.id or photoshoots.id
                $table->string('custom_title', 255)->nullable();
                $table->string('custom_badge', 100)->nullable();
                $table->text('custom_notes')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                // Unique constraint per landing page and item
                $table->unique(['landing_page_id', 'item_type', 'item_id'], 'landing_page_item_unique');

                // Indexes & Constraints
                $table->index(['landing_page_id', 'sort_order']);
                $table->index(['item_type', 'item_id']);
                $table->index('is_active');
                $table->foreign('landing_page_id')->references('id')->on('seo_landing_pages')->onDelete('cascade');
                $table->foreign('section_id')->references('id')->on('seo_landing_page_sections')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_landing_page_items');
    }
};
