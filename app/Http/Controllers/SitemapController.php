<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;
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
     * Master Sitemap Index: provides references to core pages and dynamic prompt chunks
     */
    public function index()
    {
        $xml = Cache::remember('sitemap_index_xml', now()->addHours(6), function () {
            $totalPrompts = Images::where('status', 'active')->count();
            $totalPages = max(1, (int) ceil($totalPrompts / self::CHUNK_SIZE));

            $latestPromptDate = Images::where('status', 'active')->max('date');
            $latestMod = $latestPromptDate
                ? Carbon::parse($latestPromptDate)->format('Y-m-d')
                : Carbon::now()->format('Y-m-d');

            $promptChunks = [];
            for ($page = 1; $page <= $totalPages; $page++) {
                $offset = ($page - 1) * self::CHUNK_SIZE;
                $chunkDate = Images::where('status', 'active')
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
     * Core Pages Sitemap (Home, Categories, Blog, Pages, etc.)
     */
    public function main()
    {
        $xml = Cache::remember('sitemap_main_xml', now()->addHours(12), function () {
            return view('default.sitemaps')->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Dynamic chunk of prompt image URLs (e.g. sitemaps-prompts-1.xml)
     */
    public function prompts($page = 1)
    {
        $page = (int) $page;
        if ($page < 1) {
            abort(404);
        }

        $xml = Cache::remember("sitemap_prompts_{$page}_xml", now()->addHours(6), function () use ($page) {
            $offset = ($page - 1) * self::CHUNK_SIZE;

            $images = Images::select(['id', 'title', 'slug', 'thumbnail', 'preview', 'date'])
                ->where('status', 'active')
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
