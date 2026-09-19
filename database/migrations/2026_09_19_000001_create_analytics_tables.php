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
        if (!Schema::hasTable('analytics_sessions')) {
            Schema::create('analytics_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 64)->unique();
                $table->string('visitor_id', 64)->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->boolean('is_new_visitor')->default(false);
                $table->boolean('is_new_session')->default(true);
                $table->tinyInteger('is_bot')->default(0)->index()->comment('0=User, 1=Bot, 2=Suspicious');
                $table->string('bot_name', 100)->nullable();
                $table->string('entry_page', 255)->nullable();
                $table->string('exit_page', 255)->nullable();
                $table->text('referrer')->nullable();
                $table->string('referrer_domain', 150)->nullable();
                $table->string('utm_source', 100)->nullable();
                $table->string('utm_medium', 100)->nullable();
                $table->string('utm_campaign', 100)->nullable();
                $table->string('device_type', 20)->default('desktop');
                $table->string('browser', 50)->nullable();
                $table->string('os', 50)->nullable();
                $table->char('country_code', 2)->nullable();
                $table->string('city', 100)->nullable();
                $table->unsignedInteger('page_views_count')->default(1);
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->boolean('is_engaged')->default(false);
                $table->timestamp('started_at')->nullable()->index();
                $table->timestamp('last_activity_at')->nullable()->index();

                $table->index(['started_at', 'is_bot']);
                $table->index(['last_activity_at', 'is_bot']);
            });
        }

        if (!Schema::hasTable('analytics_events')) {
            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 64)->index();
                $table->string('visitor_id', 64)->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('event_name', 50)->index();
                $table->string('page_url', 255)->nullable();
                $table->unsignedBigInteger('image_id')->nullable()->index();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable()->index();

                $table->index(['event_name', 'created_at']);
                $table->index(['image_id', 'event_name']);
            });
        }

        if (!Schema::hasTable('analytics_searches')) {
            Schema::create('analytics_searches', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 64)->index();
                $table->string('visitor_id', 64)->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('query', 255)->index();
                $table->unsignedInteger('results_count')->default(0);
                $table->string('tier_filter', 20)->nullable();
                $table->string('ai_model_filter', 50)->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        if (!Schema::hasTable('analytics_daily_summary')) {
            Schema::create('analytics_daily_summary', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->unsignedInteger('total_visitors')->default(0);
                $table->unsignedInteger('unique_visitors')->default(0);
                $table->unsignedInteger('new_visitors')->default(0);
                $table->unsignedInteger('returning_visitors')->default(0);
                $table->unsignedInteger('logged_in_users')->default(0);
                $table->unsignedInteger('anonymous_users')->default(0);
                $table->unsignedInteger('total_sessions')->default(0);
                $table->unsignedInteger('total_page_views')->default(0);
                $table->unsignedInteger('prompt_views')->default(0);
                $table->unsignedInteger('prompt_copies')->default(0);
                $table->unsignedInteger('total_searches')->default(0);
                $table->unsignedInteger('pricing_views')->default(0);
                $table->unsignedInteger('checkout_starts')->default(0);
                $table->unsignedInteger('payment_attempts')->default(0);
                $table->unsignedInteger('payment_successes')->default(0);
                $table->timestamps();
            });
        }

        // Add 'analytics' permission to roles_and_permissions if not present
        try {
            $roles = DB::table('roles_and_permissions')->get();
            foreach ($roles as $role) {
                if ($role->permissions !== 'full_access' && $role->permissions !== 'limited_access') {
                    $perms = array_filter(explode(',', $role->permissions));
                    if (!in_array('analytics', $perms)) {
                        $perms[] = 'analytics';
                        DB::table('roles_and_permissions')
                            ->where('id', $role->id)
                            ->update(['permissions' => implode(',', $perms)]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log or ignore if table is empty or structure differs
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_daily_summary');
        Schema::dropIfExists('analytics_searches');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('analytics_sessions');
    }
};
