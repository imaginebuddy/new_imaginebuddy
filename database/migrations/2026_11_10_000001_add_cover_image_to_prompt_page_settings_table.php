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
        if (Schema::hasTable('prompt_page_settings') && !Schema::hasColumn('prompt_page_settings', 'cover_image')) {
            Schema::table('prompt_page_settings', function (Blueprint $table) {
                $table->string('cover_image')->nullable()->after('hero_subtitle');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('prompt_page_settings') && Schema::hasColumn('prompt_page_settings', 'cover_image')) {
            Schema::table('prompt_page_settings', function (Blueprint $table) {
                $table->dropColumn('cover_image');
            });
        }
    }
};
