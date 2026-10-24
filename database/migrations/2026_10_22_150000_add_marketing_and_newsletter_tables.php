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
        // 1. Add marketing consent columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'marketing_opt_in')) {
                $table->tinyInteger('marketing_opt_in')->default(0)->after('status');
            }
            if (!Schema::hasColumn('users', 'marketing_opt_in_at')) {
                $table->timestamp('marketing_opt_in_at')->nullable()->after('marketing_opt_in');
            }
            if (!Schema::hasColumn('users', 'marketing_opt_in_ip')) {
                $table->string('marketing_opt_in_ip', 45)->nullable()->after('marketing_opt_in_at');
            }
            if (!Schema::hasColumn('users', 'marketing_unsubscribed_at')) {
                $table->timestamp('marketing_unsubscribed_at')->nullable()->after('marketing_opt_in_ip');
            }
        });

        // 2. Harden two_factor_codes table with expiration and attempt counters
        if (Schema::hasTable('two_factor_codes')) {
            Schema::table('two_factor_codes', function (Blueprint $table) {
                if (!Schema::hasColumn('two_factor_codes', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('code');
                }
                if (!Schema::hasColumn('two_factor_codes', 'attempts')) {
                    $table->tinyInteger('attempts')->default(0)->after('expires_at');
                }
            });
        }

        // 3. Create newsletter_subscribers table for non-registered lead capture
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->enum('status', ['pending_verification', 'subscribed', 'unsubscribed'])->default('pending_verification');
                $table->string('verification_token', 100);
                $table->string('source_page', 100)->default('footer');
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('unsubscribed_at')->nullable();
                $table->timestamps();

                $table->index('status');
                $table->index('verification_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['marketing_opt_in', 'marketing_opt_in_at', 'marketing_opt_in_ip', 'marketing_unsubscribed_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasTable('two_factor_codes')) {
            Schema::table('two_factor_codes', function (Blueprint $table) {
                if (Schema::hasColumn('two_factor_codes', 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
                if (Schema::hasColumn('two_factor_codes', 'attempts')) {
                    $table->dropColumn('attempts');
                }
            });
        }

        Schema::dropIfExists('newsletter_subscribers');
    }
};
