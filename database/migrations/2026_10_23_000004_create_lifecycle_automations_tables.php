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
        // 1. Create abandoned_checkout_logs table
        if (!Schema::hasTable('abandoned_checkout_logs')) {
            Schema::create('abandoned_checkout_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email');
                $table->string('plan_id', 100)->nullable();
                $table->timestamp('sent_at');
                $table->timestamps();

                $table->index('email');
                $table->index('sent_at');
            });
        }

        // 2. Add welcome_drip_sent_at to users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'welcome_drip_sent_at')) {
                    $table->timestamp('welcome_drip_sent_at')->nullable()->after('marketing_opt_in_at');
                }
            });
        }

        // 3. Add welcome_drip_sent_at to newsletter_subscribers
        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletter_subscribers', 'welcome_drip_sent_at')) {
                    $table->timestamp('welcome_drip_sent_at')->nullable()->after('confirmed_at');
                }
            });
        }

        // 4. Add alert columns to subscriptions
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'last_expiration_alert_sent_at')) {
                    $table->timestamp('last_expiration_alert_sent_at')->nullable()->after('ends_at');
                }
                if (!Schema::hasColumn('subscriptions', 'last_quota_alert_sent_at')) {
                    $table->timestamp('last_quota_alert_sent_at')->nullable()->after('last_expiration_alert_sent_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_checkout_logs');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'welcome_drip_sent_at')) {
                    $table->dropColumn('welcome_drip_sent_at');
                }
            });
        }

        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (Schema::hasColumn('newsletter_subscribers', 'welcome_drip_sent_at')) {
                    $table->dropColumn('welcome_drip_sent_at');
                }
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'last_expiration_alert_sent_at')) {
                    $table->dropColumn('last_expiration_alert_sent_at');
                }
                if (Schema::hasColumn('subscriptions', 'last_quota_alert_sent_at')) {
                    $table->dropColumn('last_quota_alert_sent_at');
                }
            });
        }
    }
};
