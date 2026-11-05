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
                if (!Schema::hasColumn('seo_landing_pages', 'before_image')) {
                    $table->string('before_image', 255)->nullable()->after('featured_image');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'after_image')) {
                    $table->string('after_image', 255)->nullable()->after('before_image');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'before_label')) {
                    $table->string('before_label', 100)->nullable()->default('Raw Snapshot')->after('after_image');
                }
                if (!Schema::hasColumn('seo_landing_pages', 'after_label')) {
                    $table->string('after_label', 100)->nullable()->default('Campaign Ready')->after('before_label');
                }
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
                $columns = ['before_image', 'after_image', 'before_label', 'after_label'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('seo_landing_pages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
