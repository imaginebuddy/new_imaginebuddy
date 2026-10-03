<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SeoMetadata;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $exists = DB::table('seo_metadata')->where('page_key', 'blog_index')->exists();
        if (!$exists) {
            DB::table('seo_metadata')->insert([
                'page_key' => 'blog_index',
                'page_name' => 'Blog & Articles Hub',
                'route_name' => 'blog.index',
                'path' => '/blog',
                'type' => 'static_page',
                'meta_title' => 'Blog & AI Photography Tutorials - ImagineBuddy',
                'meta_description' => 'Explore the latest tutorials, commercial prompt engineering guides, AI photoshoot workflows, and creative strategies on ImagineBuddy.',
                'meta_keywords' => 'AI blog, prompt engineering blog, photoshoot tutorials, Midjourney guides, AI commercial photography',
                'canonical_url' => null,
                'robots' => 'index, follow',
                'og_title' => 'Blog & AI Photography Tutorials - ImagineBuddy',
                'og_description' => 'Explore the latest tutorials, commercial prompt engineering guides, AI photoshoot workflows, and creative strategies on ImagineBuddy.',
                'og_image' => null,
                'twitter_card' => 'summary_large_image',
                'schema_type' => 'Blog',
                'schema_custom' => null,
                'is_active' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Cache::forget(SeoMetadata::CACHE_KEY);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('seo_metadata')->where('page_key', 'blog_index')->delete();
        Cache::forget(SeoMetadata::CACHE_KEY);
    }
};
