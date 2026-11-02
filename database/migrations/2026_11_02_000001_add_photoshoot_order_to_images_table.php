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
        if (Schema::hasTable('images') && !Schema::hasColumn('images', 'photoshoot_order')) {
            Schema::table('images', function (Blueprint $table) {
                $table->unsignedInteger('photoshoot_order')->default(0)->after('photoshoot_id');
                $table->index(['photoshoot_id', 'photoshoot_order']);
            });

            // Initialize existing photoshoot prompts with sequential photoshoot_order based on their current id desc
            $photoshootIds = DB::table('images')
                ->whereNotNull('photoshoot_id')
                ->distinct()
                ->pluck('photoshoot_id');

            foreach ($photoshootIds as $psId) {
                $prompts = DB::table('images')
                    ->where('photoshoot_id', $psId)
                    ->orderBy('id', 'desc')
                    ->select('id')
                    ->get();

                $order = 1;
                foreach ($prompts as $prompt) {
                    DB::table('images')
                        ->where('id', $prompt->id)
                        ->update(['photoshoot_order' => $order]);
                    $order++;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('images') && Schema::hasColumn('images', 'photoshoot_order')) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropIndex(['photoshoot_id', 'photoshoot_order']);
                $table->dropColumn('photoshoot_order');
            });
        }
    }
};
