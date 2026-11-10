<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Images;
use App\Models\PromptCollection;
use App\Models\PromptPageSetting;
use Illuminate\Http\Request;

class PromptsDirectoryController extends Controller
{
    /**
     * Display public /prompts hub directory.
     */
    public function index(Request $request)
    {
        $promptSettings = PromptPageSetting::getSettings();

        $query = PromptCollection::where('status', 'active')
            ->with(['category', 'prompts' => function ($q) {
                $q->where('images.status', 'active');
            }])
            ->withCount(['prompts as prompts_count' => function ($q) {
                $q->where('images.status', 'active');
            }]);

        // Search query
        $searchQuery = trim($request->get('q', ''));
        if (!empty($searchQuery)) {
            $query->where(function ($sq) use ($searchQuery) {
                $sq->where('title', 'LIKE', "%{$searchQuery}%")
                   ->orWhere('summary', 'LIKE', "%{$searchQuery}%")
                   ->orWhere('slug', 'LIKE', "%{$searchQuery}%")
                   ->orWhere('ai_engine', 'LIKE', "%{$searchQuery}%");
            });
        }

        // Category filter by slug
        $currentCategory = null;
        if ($request->filled('category')) {
            $categorySlug = trim($request->category);
            $currentCategory = Categories::where('slug', $categorySlug)->first();
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }

        // Sorting logic
        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'prompts':
                $query->orderBy('prompts_count', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('id', 'desc');
                break;
            case 'featured':
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('id', 'desc');
                break;
        }

        $collections = $query->paginate(12)->appends($request->query());

        // Get categories that have at least one active collection
        $categories = Categories::where('mode', 'on')
            ->whereHas('promptCollections', function ($q) {
                $q->where('status', 'active');
            })
            ->withCount(['promptCollections' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        $totalCollectionsCount = PromptCollection::where('status', 'active')->count();

        return view('prompts.index', compact(
            'promptSettings',
            'collections',
            'categories',
            'currentCategory',
            'searchQuery',
            'totalCollectionsCount',
            'sort'
        ));
    }

    /**
     * Display dynamic /prompts/{slug} landing page.
     */
    public function show($slug)
    {
        $collection = PromptCollection::where('slug', $slug)
            ->with(['category'])
            ->firstOrFail();

        // Check if draft and non-admin
        if ($collection->status === 'draft') {
            $user = auth()->user();
            if (!$user || ($user->role !== 'admin' && (!method_exists($user, 'hasPermission') || !$user->hasPermission('full_access')))) {
                abort(404);
            }
        }

        // Atomic view count increment
        $collection->increment('views_count');

        // Load prompt images attached to this collection via pivot with identical structure to PhotoshootsController
        $images = Images::selectFieldsRelation()
            ->join('prompt_collection_items', 'images.id', '=', 'prompt_collection_items.image_id')
            ->where('prompt_collection_items.prompt_collection_id', $collection->id)
            ->where('images.status', 'active')
            ->addSelect([
                'prompt_collection_items.custom_prompt_override',
                'prompt_collection_items.custom_shot_type',
                'prompt_collection_items.sort_order',
            ])
            ->orderBy('prompt_collection_items.sort_order', 'asc')
            ->orderBy('images.id', 'desc')
            ->paginate(12);

        $images->load('examples');

        // Apply custom prompt override to the prompt text if defined
        $images->getCollection()->transform(function ($img) {
            if (!empty($img->custom_prompt_override)) {
                $img->prompt = $img->custom_prompt_override;
            }
            return $img;
        });

        // AJAX / JSON infinite scroll response (matching PhotoshootsController)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('includes.images', ['images' => $images])->render(),
                'hasMore' => $images->hasMorePages(),
                'nextPage' => $images->hasMorePages() ? ($images->currentPage() + 1) : null,
                'total' => $images->total(),
                'count' => $images->count(),
            ]);
        }

        // Related collections for cross-linking & SEO link equity
        $relatedCollections = PromptCollection::where('status', 'active')
            ->where('id', '!=', $collection->id)
            ->where(function ($q) use ($collection) {
                if ($collection->category_id) {
                    $q->where('category_id', $collection->category_id);
                }
            })
            ->with(['category', 'prompts' => function ($q) {
                $q->where('images.status', 'active');
            }])
            ->withCount(['prompts as prompts_count' => function ($q) {
                $q->where('images.status', 'active');
            }])
            ->take(3)
            ->get();

        // If not enough related from same category, backfill with featured or latest
        if ($relatedCollections->count() < 3) {
            $excludeIds = $relatedCollections->pluck('id')->push($collection->id)->toArray();
            $moreCollections = PromptCollection::where('status', 'active')
                ->whereNotIn('id', $excludeIds)
                ->with(['category', 'prompts' => function ($q) {
                    $q->where('images.status', 'active');
                }])
                ->withCount(['prompts as prompts_count' => function ($q) {
                    $q->where('images.status', 'active');
                }])
                ->take(3 - $relatedCollections->count())
                ->get();

            $relatedCollections = $relatedCollections->concat($moreCollections);
        }

        $promptSettings = PromptPageSetting::getSettings();
        $testimonials = class_exists(\App\Models\Testimonial::class) ? \App\Models\Testimonial::active()->ordered()->get() : collect();

        return view('prompts.show', compact('collection', 'images', 'relatedCollections', 'promptSettings', 'testimonials'));
    }
}
