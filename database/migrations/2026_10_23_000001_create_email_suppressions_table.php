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
        if (!Schema::hasTable('email_suppressions')) {
            Schema::create('email_suppressions', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->enum('reason', ['hard_bounce', 'complaint', 'manual_unsubscribe', 'admin_suppressed'])->default('hard_bounce');
                $table->string('source_stream', 50)->default('updates');
                $table->text('raw_payload')->nullable();
                $table->timestamps();

                $table->index('email');
                $table->index('reason');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
