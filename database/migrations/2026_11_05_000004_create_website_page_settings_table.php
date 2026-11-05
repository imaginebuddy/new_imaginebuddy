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
        if (!Schema::hasTable('website_page_settings')) {
            Schema::create('website_page_settings', function (Blueprint $table) {
                $table->id();
                $table->string('page_key', 64)->unique();
                $table->text('page_title')->nullable();
                $table->text('subtitle')->nullable();
                $table->string('seo_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->json('extra_data')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_page_settings');
    }
};
