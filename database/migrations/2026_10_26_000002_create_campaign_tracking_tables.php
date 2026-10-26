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
        // 1. Add analytics & progress columns to email_campaigns table
        if (Schema::hasTable('email_campaigns')) {
            Schema::table('email_campaigns', function (Blueprint $table) {
                if (!Schema::hasColumn('email_campaigns', 'processed_count')) {
                    $table->integer('processed_count')->default(0)->after('sent_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'delivered_count')) {
                    $table->integer('delivered_count')->default(0)->after('processed_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'bounced_count')) {
                    $table->integer('bounced_count')->default(0)->after('delivered_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'complained_count')) {
                    $table->integer('complained_count')->default(0)->after('bounced_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'failed_count')) {
                    $table->integer('failed_count')->default(0)->after('complained_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'unique_opens_count')) {
                    $table->integer('unique_opens_count')->default(0)->after('failed_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'total_opens_count')) {
                    $table->integer('total_opens_count')->default(0)->after('unique_opens_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'unique_clicks_count')) {
                    $table->integer('unique_clicks_count')->default(0)->after('total_opens_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'total_clicks_count')) {
                    $table->integer('total_clicks_count')->default(0)->after('unique_clicks_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'track_opens')) {
                    $table->boolean('track_opens')->default(1)->after('total_clicks_count');
                }
                if (!Schema::hasColumn('email_campaigns', 'track_clicks')) {
                    $table->boolean('track_clicks')->default(1)->after('track_opens');
                }
                if (!Schema::hasColumn('email_campaigns', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('sent_at');
                }
            });
        }

        // 2. Create campaign_recipients table
        if (!Schema::hasTable('campaign_recipients')) {
            Schema::create('campaign_recipients', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->string('recipient_email', 255);
                $table->string('recipient_name', 255)->nullable();
                $table->string('tracking_token', 64)->unique();
                $table->enum('status', [
                    'queued',
                    'sending',
                    'sent',
                    'delivered',
                    'soft_bounced',
                    'hard_bounced',
                    'complained',
                    'failed',
                ])->default('queued');
                $table->string('ses_message_id', 128)->nullable()->index();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->integer('open_count')->default(0);
                $table->timestamp('clicked_at')->nullable();
                $table->integer('click_count')->default(0);
                $table->timestamp('bounced_at')->nullable();
                $table->string('bounce_type', 64)->nullable();
                $table->timestamp('complained_at')->nullable();
                $table->timestamps();

                $table->index(['campaign_id', 'status']);
                $table->index(['campaign_id', 'recipient_email']);
                $table->index('tracking_token');
            });
        }

        // 3. Create campaign_link_clicks table
        if (!Schema::hasTable('campaign_link_clicks')) {
            Schema::create('campaign_link_clicks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->unsignedBigInteger('recipient_id');
                $table->text('target_url');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('clicked_at');
                $table->timestamps();

                $table->index(['campaign_id', 'recipient_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_link_clicks');
        Schema::dropIfExists('campaign_recipients');

        if (Schema::hasTable('email_campaigns')) {
            Schema::table('email_campaigns', function (Blueprint $table) {
                $table->dropColumn([
                    'processed_count',
                    'delivered_count',
                    'bounced_count',
                    'complained_count',
                    'failed_count',
                    'unique_opens_count',
                    'total_opens_count',
                    'unique_clicks_count',
                    'total_clicks_count',
                    'track_opens',
                    'track_clicks',
                    'completed_at',
                ]);
            });
        }
    }
};
