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
        if (!Schema::hasTable('photoshoots')) {
            Schema::create('photoshoots', function (Blueprint $table) {
                $table->id();
                $table->string('uuid', 64)->unique();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('categories_id')->nullable();
                $table->unsignedInteger('prompts_count')->default(0);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('images', 'photoshoot_id')) {
            Schema::table('images', function (Blueprint $table) {
                $table->unsignedBigInteger('photoshoot_id')->nullable()->after('subcategories_id');
                $table->index('photoshoot_id');
                $table->foreign('photoshoot_id')->references('id')->on('photoshoots')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('images', 'photoshoot_id')) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeign(['photoshoot_id']);
                $table->dropColumn('photoshoot_id');
            });
        }

        Schema::dropIfExists('photoshoots');
    }
};
