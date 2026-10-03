<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use App\Models\RolesAndPermissions;
use App\Models\UrlRedirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Image;
use Purify;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $query = BlogPost::with(['category', 'user', 'tags']);

        // Search by keyword
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('excerpt', 'LIKE', '%' . $search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $categories = BlogCategory::ordered()->get();

        return view('admin.blog-posts', compact('data', 'categories'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $categories = BlogCategory::active()->ordered()->get();
        $authors = $this->getSuperAdminAuthors();

        return view('admin.blog-post-create', compact('categories', 'authors'));
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $superAdminIds = $this->getSuperAdminUserIds();

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'user_id' => ['required', 'exists:users,id', Rule::in($superAdminIds)],
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'status' => 'required|in:published,draft,scheduled',
            'published_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'featured_image_alt' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'schema_type' => 'nullable|string|max:50',
        ], [
            'user_id.in' => 'The selected author must be a Super Admin.',
        ]);

        // Generate clean unique slug
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->title);

        if (empty($slug)) {
            $slug = 'post-' . time() . '-' . rand(100, 999);
        }

        $originalSlug = $slug;
        $counter = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Process featured image if uploaded
        $imageName = null;
        if ($request->hasFile('featured_image')) {
            $imageName = $this->handleImageUpload($request->file('featured_image'));
        }

        // Publication timestamp logic
        $publishedAt = $request->published_at ? date('Y-m-d H:i:s', strtotime($request->published_at)) : now();
        if ($request->status === 'draft') {
            $publishedAt = $request->published_at ? date('Y-m-d H:i:s', strtotime($request->published_at)) : null;
        }

        // Sanitize rich HTML content to prevent XSS
        $sanitizedContent = Purify::clean($request->content);
        $readingTime = BlogPost::estimateReadingTime($sanitizedContent);

        // Auto-generate excerpt if blank
        $excerpt = $request->filled('excerpt')
            ? trim($request->excerpt)
            : Str::limit(strip_tags($sanitizedContent), 200);

        $post = BlogPost::create([
            'user_id' => $request->user_id,
            'blog_category_id' => $request->blog_category_id ?: null,
            'title' => trim($request->title),
            'slug' => $slug,
            'preview_token' => Str::random(48),
            'excerpt' => $excerpt,
            'content' => $sanitizedContent,
            'featured_image' => $imageName,
            'featured_image_alt' => $request->featured_image_alt ? trim($request->featured_image_alt) : null,
            'status' => $request->status,
            'is_featured' => $request->has('is_featured') ? true : false,
            'reading_time' => $readingTime,
            'published_at' => $publishedAt,
            'meta_title' => $request->meta_title ? trim($request->meta_title) : null,
            'meta_description' => $request->meta_description ? trim($request->meta_description) : null,
            'meta_keywords' => $request->meta_keywords ? trim($request->meta_keywords) : null,
            'canonical_url' => $request->canonical_url ? trim($request->canonical_url) : null,
            'robots' => $request->robots ?: 'index, follow',
            'og_title' => $request->og_title ? trim($request->og_title) : null,
            'og_description' => $request->og_description ? trim($request->og_description) : null,
            'twitter_card' => 'summary_large_image',
            'schema_type' => $request->schema_type ?: 'BlogPosting',
        ]);

        // Sync Tags
        if ($request->filled('tags')) {
            $this->syncPostTags($post, $request->tags);
        }

        return redirect('panel/admin/blog')->withSuccessMessage(trans('admin.success_add'));
    }

    /**
     * Show the form for editing an existing blog post.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $post = BlogPost::with(['tags'])->findOrFail($id);
        $categories = BlogCategory::active()->ordered()->get();
        $authors = $this->getSuperAdminAuthors();
        $tagsString = $post->tags->pluck('name')->implode(', ');

        return view('admin.blog-post-edit', compact('post', 'categories', 'authors', 'tagsString'));
    }

    /**
     * Update an existing blog post.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $post = BlogPost::findOrFail($id);
        $superAdminIds = $this->getSuperAdminUserIds();

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $post->id,
            'user_id' => ['required', 'exists:users,id', Rule::in($superAdminIds)],
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'status' => 'required|in:published,draft,scheduled',
            'published_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'featured_image_alt' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'schema_type' => 'nullable|string|max:50',
        ], [
            'user_id.in' => 'The selected author must be a Super Admin.',
        ]);

        $oldSlug = $post->slug;
        $newSlug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->title);

        if (empty($newSlug)) {
            $newSlug = $oldSlug;
        }

        // Ensure slug uniqueness
        $originalSlug = $newSlug;
        $counter = 1;
        while (BlogPost::where('slug', $newSlug)->where('id', '!=', $post->id)->exists()) {
            $newSlug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Automatic 301 URL redirect if slug changed
        if ($oldSlug !== $newSlug) {
            $sourceUrl = '/blog/' . $oldSlug;
            $destUrl = '/blog/' . $newSlug;

            $existsRedirect = UrlRedirect::where('source_url', $sourceUrl)->first();
            if ($existsRedirect) {
                $existsRedirect->update(['destination_url' => $destUrl]);
            } else {
                UrlRedirect::create([
                    'source_url' => $sourceUrl,
                    'destination_url' => $destUrl,
                    'redirect_type' => 301,
                    'status' => true,
                    'notes' => 'Auto-generated on blog post slug change',
                ]);
            }
        }

        // Handle image replacement or removal
        $imageName = $post->featured_image;
        if ($request->hasFile('featured_image')) {
            $this->deleteImageFile($post->featured_image);
            $imageName = $this->handleImageUpload($request->file('featured_image'));
        } elseif ($request->boolean('remove_featured_image')) {
            $this->deleteImageFile($post->featured_image);
            $imageName = null;
        }

        // Publication timestamp logic
        $publishedAt = $post->published_at;
        if ($request->filled('published_at')) {
            $publishedAt = date('Y-m-d H:i:s', strtotime($request->published_at));
        } elseif ($request->status === 'published' && !$post->published_at) {
            $publishedAt = now();
        }

        // Sanitize rich HTML content
        $sanitizedContent = Purify::clean($request->content);
        $readingTime = BlogPost::estimateReadingTime($sanitizedContent);

        $excerpt = $request->filled('excerpt')
            ? trim($request->excerpt)
            : Str::limit(strip_tags($sanitizedContent), 200);

        $post->update([
            'user_id' => $request->user_id,
            'blog_category_id' => $request->blog_category_id ?: null,
            'title' => trim($request->title),
            'slug' => $newSlug,
            'excerpt' => $excerpt,
            'content' => $sanitizedContent,
            'featured_image' => $imageName,
            'featured_image_alt' => $request->featured_image_alt ? trim($request->featured_image_alt) : null,
            'status' => $request->status,
            'is_featured' => $request->has('is_featured') ? true : false,
            'reading_time' => $readingTime,
            'published_at' => $publishedAt,
            'meta_title' => $request->meta_title ? trim($request->meta_title) : null,
            'meta_description' => $request->meta_description ? trim($request->meta_description) : null,
            'meta_keywords' => $request->meta_keywords ? trim($request->meta_keywords) : null,
            'canonical_url' => $request->canonical_url ? trim($request->canonical_url) : null,
            'robots' => $request->robots ?: 'index, follow',
            'og_title' => $request->og_title ? trim($request->og_title) : null,
            'og_description' => $request->og_description ? trim($request->og_description) : null,
            'schema_type' => $request->schema_type ?: 'BlogPosting',
        ]);

        // Re-sync tags
        $this->syncPostTags($post, $request->tags ?? '');

        return redirect('panel/admin/blog')->withSuccessMessage(trans('admin.success_update'));
    }

    /**
     * Remove the specified post.
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $post = BlogPost::findOrFail($id);
        $this->deleteImageFile($post->featured_image);
        $post->tags()->detach();
        $post->delete();

        return redirect('panel/admin/blog')->withSuccessMessage(trans('admin.success_delete'));
    }

    /**
     * Toggle post status between published and draft.
     */
    public function toggleStatus($id)
    {
        if (!auth()->user()->hasPermission('blog')) {
            return view('admin.unauthorized');
        }

        $post = BlogPost::findOrFail($id);
        $newStatus = ($post->status === 'published') ? 'draft' : 'published';
        $post->status = $newStatus;

        if ($newStatus === 'published' && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        return back()->withSuccessMessage('Article status changed to ' . ucfirst($newStatus));
    }

    /**
     * Helper to process, resize and store uploaded featured image.
     */
    protected function handleImageUpload($file): string
    {
        $path = config('path.blog', 'uploads/blog/');
        $fullDir = public_path($path);
        if (!File::exists($fullDir)) {
            File::makeDirectory($fullDir, 0755, true, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $imageName = 'blog-' . Str::random(16) . '-' . time() . '.' . $extension;

        // Resize to optimal dimensions (max 1600px width), preserving aspect ratio
        $img = Image::make($file)->orientate()->resize(1600, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension, 90);

        $fullPath = $fullDir . $imageName;
        file_put_contents($fullPath, (string) $img);
        Storage::put($path . $imageName, (string) $img, 'public');

        return $imageName;
    }

    /**
     * Helper to safely remove image from disk.
     */
    protected function deleteImageFile(?string $imageName): void
    {
        if (!empty($imageName)) {
            $path = config('path.blog', 'uploads/blog/');
            Storage::delete($path . $imageName);
            $fullPath = public_path($path . $imageName);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }

    /**
     * Helper to synchronize tags with post.
     */
    protected function syncPostTags(BlogPost $post, string $rawTags): void
    {
        $tags = array_filter(array_map('trim', explode(',', $rawTags)));
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (empty($tagName)) continue;

            $tagSlug = Str::slug($tagName);
            $tag = BlogTag::firstOrCreate(
                ['slug' => $tagSlug],
                ['name' => $tagName]
            );

            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
    }

    /**
     * Retrieve users who have Super Admin permissions.
     */
    protected function getSuperAdminAuthors()
    {
        $superAdminRoleIds = RolesAndPermissions::where('permissions', 'full_access')->pluck('id');
        $authors = User::whereIn('role', $superAdminRoleIds)->orderBy('username')->get();

        if ($authors->isEmpty()) {
            $authors = User::where('id', 1)->get();
        }

        return $authors;
    }

    /**
     * Retrieve user IDs of Super Admins for validation.
     */
    protected function getSuperAdminUserIds(): array
    {
        return $this->getSuperAdminAuthors()->pluck('id')->toArray();
    }
}
