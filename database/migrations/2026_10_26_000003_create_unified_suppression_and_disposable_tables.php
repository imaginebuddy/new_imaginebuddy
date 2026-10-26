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
        // 1. Enhance email_suppressions table
        if (Schema::hasTable('email_suppressions')) {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `email_suppressions` MODIFY COLUMN `reason` VARCHAR(50) NOT NULL DEFAULT 'hard_bounce'");
            } catch (\Throwable $e) {
                // If already modified or not supported, continue
            }

            Schema::table('email_suppressions', function (Blueprint $table) {
                if (!Schema::hasColumn('email_suppressions', 'channel_scope')) {
                    $table->enum('channel_scope', ['all', 'marketing_only'])
                        ->default('marketing_only')
                        ->after('reason');
                }
                if (!Schema::hasColumn('email_suppressions', 'soft_bounce_count')) {
                    $table->unsignedInteger('soft_bounce_count')
                        ->default(0)
                        ->after('channel_scope');
                }
                if (!Schema::hasColumn('email_suppressions', 'last_bounce_at')) {
                    $table->timestamp('last_bounce_at')
                        ->nullable()
                        ->after('soft_bounce_count');
                }
            });
        }

        // 2. Add is_disposable_email to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'is_disposable_email')) {
                    $table->boolean('is_disposable_email')
                        ->default(0)
                        ->after('marketing_opt_in');
                }
            });
        }

        // 3. Create email_sync_logs table
        if (!Schema::hasTable('email_sync_logs')) {
            Schema::create('email_sync_logs', function (Blueprint $table) {
                $table->id();
                $table->enum('triggered_by', ['cron', 'admin'])->default('cron');
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->enum('status', ['running', 'completed', 'failed'])->default('running');
                $table->unsignedInteger('bounces_synced')->default(0);
                $table->unsignedInteger('disposables_detected')->default(0);
                $table->unsignedInteger('newly_suppressed')->default(0);
                $table->text('error_details')->nullable();
                $table->decimal('duration_seconds', 6, 2)->default(0);
                $table->timestamps();

                $table->index('triggered_by');
                $table->index('status');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('email_sync_logs')) {
            Schema::dropIfExists('email_sync_logs');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'is_disposable_email')) {
                    $table->dropColumn('is_disposable_email');
                }
            });
        }

        if (Schema::hasTable('email_suppressions')) {
            Schema::table('email_suppressions', function (Blueprint $table) {
                if (Schema::hasColumn('email_suppressions', 'channel_scope')) {
                    $table->dropColumn(['channel_scope', 'soft_bounce_count', 'last_bounce_at']);
                }
            });
        }
    }
};
