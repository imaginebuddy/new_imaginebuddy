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
        if (!Schema::hasTable('weekly_digest_history')) {
            Schema::create('weekly_digest_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id')->nullable();
                $table->string('period_key', 50)->unique();
                $table->dateTime('period_start');
                $table->dateTime('period_end');
                $table->unsignedInteger('photoshoots_count')->default(0);
                $table->json('photoshoot_ids')->nullable();
                $table->unsignedInteger('recipients_count')->default(0);
                $table->enum('status', ['dispatched', 'skipped_no_photoshoots'])->default('dispatched');
                $table->timestamps();

                $table->index('period_start');
                $table->index('period_end');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_digest_history');
    }
};
