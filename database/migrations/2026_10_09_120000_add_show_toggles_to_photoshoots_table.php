<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            if (!Schema::hasColumn('photoshoots', 'show_product_adaptability')) {
                $table->boolean('show_product_adaptability')->default(false)->after('product_adaptability');
            }
            if (!Schema::hasColumn('photoshoots', 'show_faqs')) {
                $table->boolean('show_faqs')->default(false)->after('faqs');
            }
        });

        // Set toggles to ON for existing photoshoots that already have data in the database
        DB::table('photoshoots')
            ->whereNotNull('product_adaptability')
            ->where('product_adaptability', '!=', '')
            ->where('product_adaptability', '!=', '[]')
            ->update(['show_product_adaptability' => true]);

        DB::table('photoshoots')
            ->whereNotNull('faqs')
            ->where('faqs', '!=', '')
            ->where('faqs', '!=', '[]')
            ->update(['show_faqs' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            if (Schema::hasColumn('photoshoots', 'show_product_adaptability')) {
                $table->dropColumn('show_product_adaptability');
            }
            if (Schema::hasColumn('photoshoots', 'show_faqs')) {
                $table->dropColumn('show_faqs');
            }
        });
    }
};
