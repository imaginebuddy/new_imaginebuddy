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
        Schema::table('prompt_collections', function (Blueprint $table) {
            $table->string('raw_image')->nullable()->after('cover_image');
            $table->string('transformed_image')->nullable()->after('raw_image');
            $table->boolean('show_shortcut_section')->default(true)->after('faqs');
            $table->string('shortcut_eyebrow')->nullable()->after('show_shortcut_section');
            $table->string('shortcut_title')->nullable()->after('shortcut_eyebrow');
            $table->text('shortcut_description')->nullable()->after('shortcut_title');
            $table->json('shortcut_features')->nullable()->after('shortcut_description');
            $table->string('shortcut_cta_text')->nullable()->after('shortcut_features');
            $table->string('shortcut_cta_url')->nullable()->after('shortcut_cta_text');
            $table->string('shortcut_subtext')->nullable()->after('shortcut_cta_url');
            $table->string('shortcut_compare_caption')->nullable()->after('shortcut_subtext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompt_collections', function (Blueprint $table) {
            $table->dropColumn([
                'raw_image',
                'transformed_image',
                'show_shortcut_section',
                'shortcut_eyebrow',
                'shortcut_title',
                'shortcut_description',
                'shortcut_features',
                'shortcut_cta_text',
                'shortcut_cta_url',
                'shortcut_subtext',
                'shortcut_compare_caption',
            ]);
        });
    }
};
