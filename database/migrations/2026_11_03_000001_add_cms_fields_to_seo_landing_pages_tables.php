<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('seo_landing_pages')) {
            Schema::table('seo_landing_pages', function (Blueprint $table) {
                if (!Schema::hasColumn('seo_landing_pages', 'page_type')) {
                    $table->string('page_type', 30)->default('solution')->after('category_id');
                    $table->index('page_type');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'route_prefix')) {
                    $table->string('route_prefix', 50)->nullable()->after('page_type');
                    $table->index('route_prefix');
                    $table->index(['route_prefix', 'slug']);
                }
                if (!Schema::hasColumn('seo_landing_pages', 'icon')) {
                    $table->string('icon', 100)->nullable()->after('route_prefix');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'header_badge')) {
                    $table->string('header_badge', 100)->nullable()->after('icon');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'header_cta_text')) {
                    $table->string('header_cta_text', 100)->nullable()->after('header_badge');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'header_cta_url')) {
                    $table->string('header_cta_url', 255)->nullable()->after('header_cta_text');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'header_cta_subtext')) {
                    $table->string('header_cta_subtext', 255)->nullable()->after('header_cta_url');
                }
            });
        }

        if (Schema::hasTable('seo_landing_page_sections')) {
            Schema::table('seo_landing_page_sections', function (Blueprint $table) {
                // Change enum type to string(50) so new CMS section types can be flexibly added
                $table->string('type', 50)->default('rich_text')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seo_landing_pages')) {
            Schema::table('seo_landing_pages', function (Blueprint $table) {
                $columns = ['page_type', 'route_prefix', 'icon', 'header_badge', 'header_cta_text', 'header_cta_url', 'header_cta_subtext'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('seo_landing_pages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
