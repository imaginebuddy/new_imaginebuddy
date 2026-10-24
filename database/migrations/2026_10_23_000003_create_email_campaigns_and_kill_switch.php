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
        // 1. Create email_campaigns table
        if (!Schema::hasTable('email_campaigns')) {
            Schema::create('email_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subject');
                $table->string('preview_text')->nullable();
                $table->enum('target_segment', [
                    'all_verified',
                    'google_users',
                    'newsletter_subscribers',
                    'paying_subscribers',
                    'test_list',
                ])->default('all_verified');
                $table->string('template_preset', 50)->default('prompts_showcase');
                $table->longText('content');
                $table->enum('status', [
                    'draft',
                    'queued',
                    'sending',
                    'sent',
                    'cancelled',
                ])->default('draft');
                $table->integer('recipients_count')->default(0);
                $table->integer('sent_count')->default(0);
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('status');
                $table->index('target_segment');
            });
        }

        // 2. Add bulk_email_kill_switch to admin_settings table if not exists
        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_settings', 'bulk_email_kill_switch')) {
                    $table->boolean('bulk_email_kill_switch')->default(0)->after('email_admin');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');

        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_settings', 'bulk_email_kill_switch')) {
                    $table->dropColumn('bulk_email_kill_switch');
                }
            });
        }
    }
};
