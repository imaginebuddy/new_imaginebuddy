<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SeoMetadata;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('seo_metadata')) {
            $exists = DB::table('seo_metadata')->where('page_key', 'ai_model_template')->exists();
            if (!$exists) {
                $now = now();
                DB::table('seo_metadata')->insert([
                    'page_key' => 'ai_model_template',
                    'page_name' => 'Default AI Model Detail Template',
                    'route_name' => 'ai-model.detail',
                    'path' => '/ai-model/{slug}',
                    'type' => 'dynamic_template',
                    'meta_title' => '{model} AI Prompts & Images | {site_name}',
                    'meta_description' => 'Explore tested {model} AI prompts. Generate realistic commercial visuals with tested prompts for {model} on {site_name}.',
                    'meta_keywords' => '{model} prompts, {model} AI art, {model} photography, commercial prompts',
                    'canonical_url' => null,
                    'robots' => 'index, follow',
                    'schema_type' => 'CollectionPage',
                    'is_active' => 1,
                    'is_system' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                Cache::forget(SeoMetadata::CACHE_KEY);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seo_metadata')) {
            DB::table('seo_metadata')->where('page_key', 'ai_model_template')->delete();
            Cache::forget(SeoMetadata::CACHE_KEY);
        }
    }
};
