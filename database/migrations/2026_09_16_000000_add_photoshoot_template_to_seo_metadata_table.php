<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $exists = DB::table('seo_metadata')->where('page_key', 'photoshoot_template')->exists();
            if (!$exists) {
                $now = now();
                DB::table('seo_metadata')->insert([
                    'page_key' => 'photoshoot_template',
                    'page_name' => 'Default Photoshoot Detail Template',
                    'route_name' => 'photoshoots.detail',
                    'path' => '/photoshoots/{slug}',
                    'type' => 'dynamic_template',
                    'meta_title' => '{title} - AI Photoshoot Set | {site_name}',
                    'meta_description' => 'Explore the {title} AI photoshoot session with consistent models and prompt recipes on {site_name}.',
                    'meta_keywords' => '{category}, AI photoshoot, consistent AI characters, photoshoot prompts',
                    'canonical_url' => null,
                    'robots' => 'index, follow',
                    'schema_type' => 'ImageGallery',
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
            DB::table('seo_metadata')->where('page_key', 'photoshoot_template')->delete();
            Cache::forget(SeoMetadata::CACHE_KEY);
        }
    }
};