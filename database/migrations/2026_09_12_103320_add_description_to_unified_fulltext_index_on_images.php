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
        Schema::table('images', function (Blueprint $table) {
            $table->dropFullText('images_title_tags_prompt_fulltext');
            $table->fullText(['title', 'tags', 'prompt', 'description'], 'images_search_unified_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropFullText('images_search_unified_fulltext');
            $table->fullText(['title', 'tags', 'prompt'], 'images_title_tags_prompt_fulltext');
        });
    }
};
