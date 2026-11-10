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
            $table->boolean('show_metrics_section')->default(true)->after('shortcut_compare_caption');
            $table->string('metrics_eyebrow')->nullable()->after('show_metrics_section');
            $table->string('metrics_title')->nullable()->after('metrics_eyebrow');
            $table->text('metrics_subtitle')->nullable()->after('metrics_title');
            $table->json('metrics_items')->nullable()->after('metrics_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompt_collections', function (Blueprint $table) {
            $table->dropColumn([
                'show_metrics_section',
                'metrics_eyebrow',
                'metrics_title',
                'metrics_subtitle',
                'metrics_items',
            ]);
        });
    }
};
