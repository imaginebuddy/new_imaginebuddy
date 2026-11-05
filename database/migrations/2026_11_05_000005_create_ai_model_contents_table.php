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
        if (!Schema::hasTable('ai_model_contents')) {
            Schema::create('ai_model_contents', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 100)->unique();
                $table->string('model_name', 100);
                $table->text('page_title')->nullable();
                $table->text('hero_lead')->nullable();
                $table->string('seo_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_model_contents');
    }
};
