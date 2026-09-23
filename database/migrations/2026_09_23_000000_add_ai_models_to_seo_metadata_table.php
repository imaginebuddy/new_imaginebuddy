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

        $exists = DB::table('seo_metadata')->where('page_key', 'ai_models')->exists();
        if (!$exists) {
            DB::table('seo_metadata')->insert([
                'page_key' => 'ai_models',
                'page_name' => 'AI Models',
                'route_name' => 'ai-models',
                'path' => '/ai-models',
                'type' => 'static_page',
                'meta_title' => 'All AI Models - Browse Prompts by Model | Imagine Buddy',
                'meta_description' => 'Browse all supported AI models including Gemini, Midjourney, ChatGPT, and Flux. Find curated prompt recipes for every major AI generator.',
                'meta_keywords' => 'AI models, Gemini prompts, ChatGPT prompts, Midjourney prompts, Flux prompts, AI image prompts',
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
        DB::table('seo_metadata')->where('page_key', 'ai_models')->delete();
        Cache::forget(SeoMetadata::CACHE_KEY);
    }
};
