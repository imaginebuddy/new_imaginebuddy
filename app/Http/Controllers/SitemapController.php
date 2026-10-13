<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;
use App\Models\Photoshoot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Number of prompts per sitemap chunk
     * Well below Google's 50,000 URL limit for fast generation & low memory footprint
     */
    const CHUNK_SIZE = 5000;

    /**
     * Cache duration in minutes (2 hours)
     */
    const CACHE_MINUTES = 120;

    /**
     * Invalidate all sitemap cached XML files
     */
    public static function clearCache(): void
    {
        Cache::forget('sitemap_index_xml');
        Cache::forget('sitemap_main_xml');

        $totalPrompts = Images::where('status', 'active')->count();
        $totalPages = max(1, (int) ceil($totalPrompts / self::CHUNK_SIZE));
        for ($page = 1; $page <= $totalPages + 2; $page++) {
            Cache::forget("sitemap_prompts_{$page}_xml");
        }
    }

    /**
     * Master Sitemap Index: provides references to core pages and dynamic prompt chunks
     */
    public function index(Request $request)
    {
        if ($request->has('refresh') || $request->has('clear_cache')) {
            self::clearCache();
        }

        $xml = Cache::remember('sitemap_index_xml', now()->addMinutes(self::CACHE_MINUTES), function () {
            $totalPrompts = Images::where('status', 'active')
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->count();
            $totalPages = max(1, (int) ceil($totalPrompts / self::CHUNK_SIZE));

            $latestPromptDate = Images::where('status', 'active')->max('date');
            $latestPhotoshootDate = Photoshoot::max('created_at');

            $dates = array_filter([
                $latestPromptDate ? Carbon::parse($latestPromptDate)->format('Y-m-d') : null,
                $latestPhotoshootDate ? Carbon::parse($latestPhotoshootDate)->format('Y-m-d') : null,
            ]);

            $latestMod = !empty($dates) ? max($dates) : Carbon::now()->format('Y-m-d');

            $promptChunks = [];
            for ($page = 1; $page <= $totalPages; $page++) {
                $offset = ($page - 1) * self::CHUNK_SIZE;
                $chunkDate = Images::where('status', 'active')
                    ->whereNotNull('slug')
                    ->where('slug', '!=', '')
                    ->orderBy('id', 'desc')
                    ->skip($offset)
                    ->take(self::CHUNK_SIZE)
                    ->max('date');

                $promptChunks[] = [
                    'url' => url("sitemaps-prompts-{$page}.xml"),
                    'lastmod' => $chunkDate ? Carbon::parse($chunkDate)->format('Y-m-d') : $latestMod,
                ];
            }

            return view('default.sitemaps-index', [
                'mainSitemapUrl' => url('sitemaps-main.xml'),
                'mainLastMod'    => $latestMod,
                'promptChunks'   => $promptChunks,
            ])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Core Pages Sitemap (Home, Categories, Blog, Pages, Photoshoots, etc.)
     */
    public function main(Request $request)
    {
        if ($request->has('refresh') || $request->has('clear_cache')) {
            Cache::forget('sitemap_main_xml');
        }

        $xml = Cache::remember('sitemap_main_xml', now()->addMinutes(self::CACHE_MINUTES), function () {
            return view('default.sitemaps')->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Dynamic chunk of prompt image URLs (e.g. sitemaps-prompts-1.xml)
     */
    public function prompts(Request $request, $page = 1)
    {
        $page = (int) $page;
        if ($page < 1) {
            abort(404);
        }

        if ($request->has('refresh') || $request->has('clear_cache')) {
            Cache::forget("sitemap_prompts_{$page}_xml");
        }

        $xml = Cache::remember("sitemap_prompts_{$page}_xml", now()->addMinutes(self::CACHE_MINUTES), function () use ($page) {
            $offset = ($page - 1) * self::CHUNK_SIZE;

            $images = Images::select(['id', 'title', 'slug', 'thumbnail', 'preview', 'date'])
                ->where('status', 'active')
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->orderBy('id', 'desc')
                ->skip($offset)
                ->take(self::CHUNK_SIZE)
                ->get();

            if ($images->isEmpty() && $page > 1) {
                return null;
            }

            return view('default.sitemaps-media-chunk', [
                'images' => $images,
            ])->render();
        });

        if (!$xml) {
            abort(404);
        }

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
