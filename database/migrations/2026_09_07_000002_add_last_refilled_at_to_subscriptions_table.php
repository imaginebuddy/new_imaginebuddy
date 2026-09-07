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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'last_refilled_at')) {
                $table->timestamp('last_refilled_at')->nullable()->after('last_payment');
            }
        });

        // Backfill existing subscriptions with last_refilled_at = created_at
        try {
            DB::table('subscriptions')
                ->whereNull('last_refilled_at')
                ->update(['last_refilled_at' => DB::raw('created_at')]);
        } catch (\Exception $e) {
            // Ignore if already set
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'last_refilled_at')) {
                $table->dropColumn('last_refilled_at');
            }
        });
    }
};
