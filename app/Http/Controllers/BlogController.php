<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display the public Blog Hub listing page.
     */
    public function index(Request $request)
    {
        seo()->setPage('blog_index');

        $query = BlogPost::with(['category', 'user', 'tags'])->published();

        // Optional search filter
        $searchQuery = null;
        if ($request->filled('q')) {
            $searchQuery = trim($request->q);
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'LIKE', '%' . $searchQuery . '%')
                  ->orWhere('excerpt', 'LIKE', '%' . $searchQuery . '%')
                  ->orWhere('content', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        // Spotlight featured post (only shown prominently on page 1 when not searching)
        $featuredPost = null;
        $isFirstPage = empty($request->page) || $request->page == 1;

        if ($isFirstPage && empty($searchQuery)) {
            $featuredPost = BlogPost::with(['category', 'user', 'tags'])
                ->published()
                ->featured()
                ->latest('published_at')
                ->first();

            if ($featuredPost) {
                $query->where('id', '!=', $featuredPost->id);
            }
        }

        $posts = $query->latest('published_at')->paginate(9);

        // Sidebar categories with active post count
        $categories = BlogCategory::active()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->ordered()
            ->get();

        // Popular posts for sidebar
        $popularPosts = BlogPost::published()
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        return view('blog.index', compact('posts', 'featuredPost', 'categories', 'popularPosts', 'searchQuery'));
    }

    /**
     * Display posts filtered by category.
     */
    public function category(Request $request, $slug)
    {
        $category = BlogCategory::where('slug', $slug)->active()->firstOrFail();

        seo()->setEntity($category);

        $posts = BlogPost::with(['category', 'user', 'tags'])
            ->where('blog_category_id', $category->id)
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::active()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->ordered()
            ->get();

        $popularPosts = BlogPost::published()
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        return view('blog.category', compact('category', 'posts', 'categories', 'popularPosts'));
    }

    /**
     * Display posts filtered by tag.
     */
    public function tag(Request $request, $slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        seo()->setPage('blog_index');

        $posts = $tag->publishedPosts()
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::active()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->ordered()
            ->get();

        $popularPosts = BlogPost::published()
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        $activeTag = $tag;
        $featuredPost = null;
        $searchQuery = null;

        return view('blog.index', compact('posts', 'categories', 'popularPosts', 'activeTag', 'featuredPost', 'searchQuery'));
    }

    /**
     * Display an individual blog article.
     */
    public function show($slug)
    {
        $post = BlogPost::with(['category', 'user', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views count non-destructively
        $post->increment('views_count');

        seo()->setEntity($post);

        // Fetch related articles based on category or tags
        $relatedPosts = BlogPost::with(['category'])
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, function ($q) use ($post) {
                $q->where('blog_category_id', $post->blog_category_id);
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        // If not enough related posts in the same category, fill with latest
        if ($relatedPosts->count() < 3) {
            $excludeIds = $relatedPosts->pluck('id')->push($post->id)->all();
            $fillPosts = BlogPost::with(['category'])
                ->published()
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->take(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->merge($fillPosts);
        }

        // Previous and Next article navigation links
        $prevPost = BlogPost::published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $nextPost = BlogPost::published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        $categories = BlogCategory::active()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->ordered()
            ->get();

        return view('blog.show', compact('post', 'relatedPosts', 'prevPost', 'nextPost', 'categories'));
    }

    /**
     * Preview an unpublished draft securely using preview token.
     */
    public function preview($token)
    {
        $post = BlogPost::with(['category', 'user', 'tags'])
            ->where('preview_token', $token)
            ->firstOrFail();

        seo()->setEntity($post);

        $isPreview = true;
        $relatedPosts = collect();
        $prevPost = null;
        $nextPost = null;
        $categories = BlogCategory::active()->ordered()->get();

        return view('blog.show', compact('post', 'relatedPosts', 'prevPost', 'nextPost', 'categories', 'isPreview'));
    }
}
