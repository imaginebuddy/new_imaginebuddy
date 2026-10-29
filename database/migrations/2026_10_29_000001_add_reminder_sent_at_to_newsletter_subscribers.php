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
        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletter_subscribers', 'reminder_sent_at')) {
                    $table->timestamp('reminder_sent_at')->nullable()->after('unsubscribed_at');
                }
                $table->string('status', 50)->default('pending_verification')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (Schema::hasColumn('newsletter_subscribers', 'reminder_sent_at')) {
                    $table->dropColumn('reminder_sent_at');
                }
            });
        }
    }
};
