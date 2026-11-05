<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_page_settings', function (Blueprint $table) {
            $table->id();

            // Categories Tab Header
            $table->string('page_title')->nullable();
            $table->text('page_subtitle')->nullable();

            // Tags Tab Header
            $table->string('tags_title')->nullable();
            $table->text('tags_subtitle')->nullable();

            // Search Placeholders
            $table->string('category_search_placeholder')->nullable();
            $table->string('tag_search_placeholder')->nullable();

            // SEO Meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Optional Listing Hub CTA Banner
            $table->boolean('cta_banner_enabled')->default(false);
            $table->string('cta_banner_title')->nullable();
            $table->text('cta_banner_desc')->nullable();
            $table->string('cta_banner_btn_text')->nullable();
            $table->string('cta_banner_btn_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_page_settings');
    }
};
