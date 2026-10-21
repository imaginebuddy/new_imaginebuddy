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
            if (!Schema::hasColumn('photoshoots', 'transformed_image')) {
                $table->string('transformed_image', 255)->nullable()->after('raw_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            if (Schema::hasColumn('photoshoots', 'transformed_image')) {
                $table->dropColumn('transformed_image');
            }
        });
    }
};
