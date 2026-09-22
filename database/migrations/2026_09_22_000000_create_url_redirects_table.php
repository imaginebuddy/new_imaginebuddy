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
        Schema::create('url_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url', 500);
            $table->text('destination_url');
            $table->smallInteger('redirect_type')->default(301)->index();
            $table->boolean('status')->default(true)->index();
            $table->boolean('preserve_query')->default(true);
            $table->string('notes', 255)->nullable();
            $table->unsignedBigInteger('hits_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Index source_url with a prefix length for fast matching
            $table->index([DB::raw('source_url(255)')], 'idx_source_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_redirects');
    }
};
