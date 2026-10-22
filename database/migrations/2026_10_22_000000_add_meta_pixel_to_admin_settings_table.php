<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'meta_pixel_status')) {
                $table->boolean('meta_pixel_status')->default(false)->after('google_analytics');
            }
            if (!Schema::hasColumn('admin_settings', 'meta_pixel')) {
                $table->text('meta_pixel')->nullable()->after('meta_pixel_status');
            }
            if (!Schema::hasColumn('admin_settings', 'meta_pixel_id')) {
                $table->string('meta_pixel_id', 50)->nullable()->after('meta_pixel');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admin_settings', 'meta_pixel_id')) {
                $table->dropColumn('meta_pixel_id');
            }
            if (Schema::hasColumn('admin_settings', 'meta_pixel')) {
                $table->dropColumn('meta_pixel');
            }
            if (Schema::hasColumn('admin_settings', 'meta_pixel_status')) {
                $table->dropColumn('meta_pixel_status');
            }
        });
    }
};
