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
            if (!Schema::hasColumn('photoshoots', 'product_adaptability')) {
                $table->longText('product_adaptability')->nullable()->after('creative_direction');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            if (Schema::hasColumn('photoshoots', 'product_adaptability')) {
                $table->dropColumn('product_adaptability');
            }
        });
    }
};
