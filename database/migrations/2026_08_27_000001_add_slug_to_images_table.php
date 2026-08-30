<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Images;
use App\Helper;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('images', 'slug')) {
            Schema::table('images', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Backfill existing records
        $images = Images::all();
        foreach ($images as $image) {
            if (empty($image->slug)) {
                $image->slug = Helper::createImageSlug($image->title ?: ('prompt-' . $image->id), $image->id);
                $image->save();
            }
        }

        Schema::table('images', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
