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
        Schema::table('images', function (Blueprint $table) {
            $table->text('prompt')->nullable()->after('description');
            $table->string('ai_model', 50)->nullable()->after('prompt');
            $table->unsignedBigInteger('copies_count')->default(0)->after('ai_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['prompt', 'ai_model', 'copies_count']);
        });
    }
};
