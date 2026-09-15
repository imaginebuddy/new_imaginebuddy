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
        $now = now();

        $exists = DB::table('seo_metadata')->where('page_key', 'prompts_free')->exists();
        if (!$exists) {
            DB::table('seo_metadata')->insert([
                'page_key' => 'prompts_free',
                'page_name' => 'Free Prompts',
                'route_name' => 'prompts_free',
                'path' => '/prompts/free',
                'type' => 'static_page',
                'meta_title' => 'Free AI Prompts & Creative Inspiration - ImagineBuddy',
                'meta_description' => 'Browse, copy, and create stunning visual campaigns with our 100% free AI prompt recipes for Gemini, ChatGPT, and Midjourney.',
                'meta_keywords' => 'free AI prompts, free commercial prompts, copy prompts free, AI image prompts free',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'schema_type' => 'CollectionPage',
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Cache::forget(SeoMetadata::CACHE_KEY);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('seo_metadata')->where('page_key', 'prompts_free')->delete();
        Cache::forget(SeoMetadata::CACHE_KEY);
    }
};
