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
        if (!Schema::hasColumn('admin_settings', 'daily_limit_prompts')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->unsignedInteger('daily_limit_prompts')->default(10)->after('daily_limit_downloads');
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
        if (Schema::hasColumn('admin_settings', 'daily_limit_prompts')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->dropColumn('daily_limit_prompts');
            });
        }
    }
};
