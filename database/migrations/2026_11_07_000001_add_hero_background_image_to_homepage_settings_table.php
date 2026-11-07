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
        if (Schema::hasTable('homepage_settings') && !Schema::hasColumn('homepage_settings', 'hero_background_image')) {
            Schema::table('homepage_settings', function (Blueprint $table) {
                $table->string('hero_background_image', 500)->nullable()->after('search_placeholder');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('homepage_settings') && Schema::hasColumn('homepage_settings', 'hero_background_image')) {
            Schema::table('homepage_settings', function (Blueprint $table) {
                $table->dropColumn('hero_background_image');
            });
        }
    }
};
