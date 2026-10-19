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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'is_test')) {
                $table->boolean('is_test')->default(false)->after('last_refilled_at')->index();
            }
            if (!Schema::hasColumn('subscriptions', 'test_notes')) {
                $table->string('test_notes', 255)->nullable()->after('is_test');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'test_notes')) {
                $table->dropColumn('test_notes');
            }
            if (Schema::hasColumn('subscriptions', 'is_test')) {
                $table->dropColumn('is_test');
            }
        });
    }
};
