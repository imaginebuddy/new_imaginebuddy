<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SeoMetadata;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('seo_metadata')
            ->where('page_key', 'photos_premium')
            ->orWhere('path', '/photos/premium')
            ->update([
                'page_key'   => 'prompts_premium',
                'route_name' => 'prompts_premium',
                'path'       => '/prompts/premium',
                'page_name'  => 'Premium Prompts',
                'updated_at' => now(),
            ]);

        Cache::forget(SeoMetadata::CACHE_KEY);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('seo_metadata')
            ->where('page_key', 'prompts_premium')
            ->orWhere('path', '/prompts/premium')
            ->update([
                'page_key'   => 'photos_premium',
                'route_name' => 'photos_premium',
                'path'       => '/photos/premium',
                'page_name'  => 'Premium Photos',
                'updated_at' => now(),
            ]);

        Cache::forget(SeoMetadata::CACHE_KEY);
    }
};
