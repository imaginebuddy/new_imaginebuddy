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
        if (!Schema::hasTable('anonymous_prompt_copies')) {
            Schema::create('anonymous_prompt_copies', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_id', 64)->index();
                $table->string('ip_hash', 64)->index();
                $table->unsignedBigInteger('images_id')->index();
                $table->timestamp('created_at')->nullable()->index();

                $table->index(['visitor_id', 'created_at']);
                $table->index(['ip_hash', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anonymous_prompt_copies');
    }
};
