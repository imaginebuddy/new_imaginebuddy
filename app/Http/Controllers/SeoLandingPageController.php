<?php

namespace App\Http\Controllers;

use App\Models\SeoLandingPage;
use App\Models\SeoLandingPageSection;
use App\Models\SeoLandingPageItem;
use App\Models\Images;
use App\Models\Photoshoot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoLandingPageController extends Controller
{
    /**
     * Display a published SEO Landing Page / Guide.
     */
    public function show($slug)
    {
        $page = SeoLandingPage::where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with([
                'category',
                'user',
                'sections' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order', 'asc');
                },
                'items' => function ($query) {
                    $query->where('is_active', true)
                          ->orderBy('sort_order', 'asc')
                          ->with(['prompt.author', 'prompt.category', 'photoshoot.images']);
                }
            ])
            ->firstOrFail();

        // Increment view count quietly
        $page->increment('views_count');

        // Extract prompts and photoshoots
        $prompts = $page->items->where('item_type', 'prompt')->map(function ($item) {
            $p = $item->prompt;
            if ($p) {
                $p->custom_badge = $item->custom_badge;
                $p->custom_notes = $item->custom_notes;
                $p->custom_title = $item->custom_title;
            }
            return $p;
        })->filter();

        $photoshoots = $page->items->where('item_type', 'photoshoot')->map(function ($item) {
            return $item->photoshoot;
        })->filter();

        // Calculate reading time based on total words across excerpt and sections
        $totalWords = str_word_count(strip_tags($page->excerpt ?? ''));
        foreach ($page->sections as $sec) {
            $totalWords += str_word_count(strip_tags(($sec->heading ?? '') . ' ' . ($sec->content ?? '')));
        }
        $readingTime = max(1, (int) ceil($totalWords / 200));

        // Related guides in same category
        $relatedGuides = SeoLandingPage::where('status', 'published')
            ->where('id', '!=', $page->id)
            ->where('category_id', $page->category_id)
            ->take(3)
            ->get();

        return view('guides.show', compact('page', 'prompts', 'photoshoots', 'relatedGuides', 'readingTime'));
    }

    /**
     * Preview a draft landing page via secure preview token.
     */
    public function preview($token)
    {
        $page = SeoLandingPage::where('preview_token', $token)
            ->with([
                'category',
                'user',
                'sections' => function ($query) {
                    $query->orderBy('sort_order', 'asc');
                },
                'items' => function ($query) {
                    $query->orderBy('sort_order', 'asc')
                          ->with(['prompt.author', 'prompt.category', 'photoshoot.images']);
                }
            ])
            ->firstOrFail();

        $prompts = $page->items->where('item_type', 'prompt')->map(function ($item) {
            $p = $item->prompt;
            if ($p) {
                $p->custom_badge = $item->custom_badge;
                $p->custom_notes = $item->custom_notes;
                $p->custom_title = $item->custom_title;
            }
            return $p;
        })->filter();

        $photoshoots = $page->items->where('item_type', 'photoshoot')->map(function ($item) {
            return $item->photoshoot;
        })->filter();

        $totalWords = str_word_count(strip_tags($page->excerpt ?? ''));
        foreach ($page->sections as $sec) {
            $totalWords += str_word_count(strip_tags(($sec->heading ?? '') . ' ' . ($sec->content ?? '')));
        }
        $readingTime = max(1, (int) ceil($totalWords / 200));

        $relatedGuides = collect();
        $isPreview = true;

        return response(view('guides.show', compact('page', 'prompts', 'photoshoots', 'relatedGuides', 'isPreview', 'readingTime')))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
