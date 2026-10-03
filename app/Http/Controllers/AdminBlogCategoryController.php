<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\UrlRedirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogCategoryController extends Controller
{
    /**
     * Display a listing of blog categories.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $query = BlogCategory::withCount('posts');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.blog-categories', compact('data'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        return view('admin.blog-category-create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $request->merge([
            'status' => ($request->has('status') && $request->status === 'active') ? 'active' : 'inactive',
        ]);

        $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:blog_categories,slug',
            'description' => 'nullable|string|max:1000',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        if (empty($slug)) {
            $slug = 'category-' . time();
        }

        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (BlogCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        BlogCategory::create([
            'name' => trim($request->name),
            'slug' => $slug,
            'description' => $request->description ? trim($request->description) : null,
            'seo_title' => $request->seo_title ? trim($request->seo_title) : null,
            'seo_description' => $request->seo_description ? trim($request->seo_description) : null,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'status' => $request->status,
        ]);

        return redirect('panel/admin/blog/categories')->withSuccessMessage(trans('admin.success_add'));
    }

    /**
     * Show the form for editing an existing category.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $category = BlogCategory::findOrFail($id);

        return view('admin.blog-category-edit', compact('category'));
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $category = BlogCategory::findOrFail($id);

        $request->merge([
            'status' => ($request->has('status') && $request->status === 'active') ? 'active' : 'inactive',
        ]);

        $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:blog_categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $oldSlug = $category->slug;
        $newSlug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        if (empty($newSlug)) {
            $newSlug = $oldSlug;
        }

        // Ensure slug uniqueness
        $originalSlug = $newSlug;
        $counter = 1;
        while (BlogCategory::where('slug', $newSlug)->where('id', '!=', $category->id)->exists()) {
            $newSlug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Automatic 301 URL redirect if slug changed
        if ($oldSlug !== $newSlug) {
            $sourceUrl = '/blog/category/' . $oldSlug;
            $destUrl = '/blog/category/' . $newSlug;

            $existsRedirect = UrlRedirect::where('source_url', $sourceUrl)->first();
            if ($existsRedirect) {
                $existsRedirect->update(['destination_url' => $destUrl]);
            } else {
                UrlRedirect::create([
                    'source_url' => $sourceUrl,
                    'destination_url' => $destUrl,
                    'redirect_type' => 301,
                    'status' => true,
                    'notes' => 'Auto-generated on blog category slug change',
                ]);
            }
        }

        $category->update([
            'name' => trim($request->name),
            'slug' => $newSlug,
            'description' => $request->description ? trim($request->description) : null,
            'seo_title' => $request->seo_title ? trim($request->seo_title) : null,
            'seo_description' => $request->seo_description ? trim($request->seo_description) : null,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'status' => $request->status,
        ]);

        return redirect('panel/admin/blog/categories')->withSuccessMessage(trans('admin.success_update'));
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect('panel/admin/blog/categories')->withSuccessMessage(trans('admin.success_delete'));
    }
}
