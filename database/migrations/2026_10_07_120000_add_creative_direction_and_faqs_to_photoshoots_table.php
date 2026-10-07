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
        Schema::table('photoshoots', function (Blueprint $table) {
            if (!Schema::hasColumn('photoshoots', 'creative_direction')) {
                $table->longText('creative_direction')->nullable()->after('ai_model');
            }
            if (!Schema::hasColumn('photoshoots', 'faqs')) {
                $table->longText('faqs')->nullable()->after('creative_direction');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            if (Schema::hasColumn('photoshoots', 'creative_direction')) {
                $table->dropColumn('creative_direction');
            }
            if (Schema::hasColumn('photoshoots', 'faqs')) {
                $table->dropColumn('faqs');
            }
        });
    }
};
