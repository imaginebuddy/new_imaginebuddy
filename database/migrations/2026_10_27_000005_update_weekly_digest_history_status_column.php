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
        if (Schema::hasTable('weekly_digest_history')) {
            Schema::table('weekly_digest_history', function (Blueprint $table) {
                $table->string('status', 50)->default('dispatched')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('weekly_digest_history')) {
            Schema::table('weekly_digest_history', function (Blueprint $table) {
                $table->enum('status', ['dispatched', 'skipped_no_photoshoots', 'stopped'])->default('dispatched')->change();
            });
        }
    }
};
