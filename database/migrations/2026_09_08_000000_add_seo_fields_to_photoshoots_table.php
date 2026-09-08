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
        Schema::table('photoshoots', function (Blueprint $table) {
            if (!Schema::hasColumn('photoshoots', 'meta_title')) {
                $table->string('meta_title', 255)->nullable()->after('description');
            }
            if (!Schema::hasColumn('photoshoots', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('photoshoots', 'meta_keywords')) {
                $table->string('meta_keywords', 255)->nullable()->after('meta_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photoshoots', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('photoshoots', 'meta_title')) {
                $columnsToDrop[] = 'meta_title';
            }
            if (Schema::hasColumn('photoshoots', 'meta_description')) {
                $columnsToDrop[] = 'meta_description';
            }
            if (Schema::hasColumn('photoshoots', 'meta_keywords')) {
                $columnsToDrop[] = 'meta_keywords';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
