<?php

namespace App\Http\Controllers;

use App\Models\SeoLandingPage;
use App\Models\SeoLandingPageSection;
use App\Models\SeoLandingPageItem;
use App\Models\Categories;
use App\Models\Images;
use App\Models\Photoshoot;
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

class AdminSeoLandingPageController extends Controller
{
    /**
     * Display a listing of SEO landing pages.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return view('admin.unauthorized');
        }

        $query = SeoLandingPage::with(['category', 'user'])->withCount(['sections', 'items']);

        // Search by keyword
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('internal_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $search . '%')
                  ->orWhere('primary_keyword', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('id', 'desc')->paginate(15);
        $categories = Categories::where('mode', 'on')->orderBy('name')->get();

        return view('admin.seo-landing-pages', compact('data', 'categories'));
    }

    /**
     * Show form to create a new landing page.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return view('admin.unauthorized');
        }

        $categories = Categories::where('mode', 'on')->orderBy('name')->get();
        $authors = $this->getSuperAdminAuthors();

        return view('admin.seo-landing-page-create', compact('categories', 'authors'));
    }

    /**
     * Store a newly created landing page.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return view('admin.unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'internal_name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:seo_landing_pages,slug',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'excerpt' => 'nullable|string|max:1000',
            'primary_keyword' => 'nullable|string|max:150',
            'secondary_keywords' => 'nullable|string|max:1000',
            'search_intent' => 'required|in:informational,commercial,transactional',
            'status' => 'required|in:published,draft,scheduled,archived',
            'published_at' => 'nullable|date',
            'scheduled_for' => 'nullable|date',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'featured_image_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'schema_type' => 'nullable|string|max:50',
        ]);

        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (SeoLandingPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $page = new SeoLandingPage();
        $page->user_id = $request->user_id;
        $page->category_id = $request->category_id ?: null;
        $page->internal_name = $request->internal_name ?: $request->title;
        $page->title = $request->title;
        $page->slug = $slug;
        $page->preview_token = Str::random(64);
        $page->excerpt = $request->excerpt;
        $page->target_audience = $request->target_audience;
        $page->primary_keyword = $request->primary_keyword;
        $page->secondary_keywords = $request->secondary_keywords;
        $page->search_intent = $request->search_intent;
        $page->status = $request->status;

        if ($request->status === 'published') {
            $page->published_at = $request->published_at ? $request->published_at : now();
        } elseif ($request->status === 'scheduled') {
            $page->scheduled_for = $request->scheduled_for;
        }

        // Upload featured image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $uploadPath = public_path('uploads/guides');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $page->featured_image = $filename;
            $page->featured_image_alt = $request->featured_image_alt ?: $request->title;
        }

        // SEO fields
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->canonical_url = $request->canonical_url;
        $page->robots = $request->robots ?: 'index, follow';
        $page->og_title = $request->og_title ?: $request->title;
        $page->og_description = $request->og_description ?: $request->excerpt;
        $page->schema_type = $request->schema_type ?: 'Article';
        $page->enable_table_of_contents = $request->boolean('enable_table_of_contents', true);
        $page->enable_prompt_copying = $request->boolean('enable_prompt_copying', true);

        $page->save();

        return redirect('panel/admin/seo-pages/edit/' . $page->id)
            ->with('success_message', 'SEO Landing Page created! Now configure sections and prompts.');
    }

    /**
     * Show form to edit landing page, manage sections, and connect prompts.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return view('admin.unauthorized');
        }

        $page = SeoLandingPage::with(['sections.items.prompt', 'items.prompt', 'items.photoshoot'])->findOrFail($id);
        $categories = Categories::where('mode', 'on')->orderBy('name')->get();
        $authors = $this->getSuperAdminAuthors();

        return view('admin.seo-landing-page-edit', compact('page', 'categories', 'authors'));
    }

    /**
     * Update landing page metadata and handle slug redirection.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return view('admin.unauthorized');
        }

        $page = SeoLandingPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'internal_name' => 'nullable|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('seo_landing_pages', 'slug')->ignore($page->id)],
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'excerpt' => 'nullable|string|max:1000',
            'primary_keyword' => 'nullable|string|max:150',
            'secondary_keywords' => 'nullable|string|max:1000',
            'search_intent' => 'required|in:informational,commercial,transactional',
            'status' => 'required|in:published,draft,scheduled,archived',
            'published_at' => 'nullable|date',
            'scheduled_for' => 'nullable|date',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'featured_image_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'schema_type' => 'nullable|string|max:50',
        ]);

        $oldSlug = $page->slug;
        $newSlug = Str::slug($request->slug);

        // Automatic 301 Redirect creation if slug changed on a published page
        if ($oldSlug !== $newSlug && $page->status === 'published') {
            UrlRedirect::create([
                'source_url' => 'guides/' . $oldSlug,
                'destination_url' => 'guides/' . $newSlug,
                'redirect_type' => 301,
                'status' => true,
                'notes' => 'Auto-generated on SEO landing page slug update: ' . $page->title,
            ]);
        }

        $page->user_id = $request->user_id;
        $page->category_id = $request->category_id ?: null;
        $page->internal_name = $request->internal_name ?: $request->title;
        $page->title = $request->title;
        $page->slug = $newSlug;
        $page->excerpt = $request->excerpt;
        $page->target_audience = $request->target_audience;
        $page->primary_keyword = $request->primary_keyword;
        $page->secondary_keywords = $request->secondary_keywords;
        $page->search_intent = $request->search_intent;
        $page->status = $request->status;

        if ($request->status === 'published') {
            $page->published_at = $request->published_at ? $request->published_at : ($page->published_at ?: now());
        } elseif ($request->status === 'scheduled') {
            $page->scheduled_for = $request->scheduled_for;
        }

        // Upload featured image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $uploadPath = public_path('uploads/guides');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            // Remove old file if exists
            if (!empty($page->featured_image) && File::exists($uploadPath . '/' . $page->featured_image)) {
                File::delete($uploadPath . '/' . $page->featured_image);
            }

            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $page->featured_image = $filename;
        }

        if ($request->filled('featured_image_alt')) {
            $page->featured_image_alt = $request->featured_image_alt;
        }

        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->canonical_url = $request->canonical_url;
        $page->robots = $request->robots ?: 'index, follow';
        $page->og_title = $request->og_title ?: $request->title;
        $page->og_description = $request->og_description ?: $request->excerpt;
        $page->schema_type = $request->schema_type ?: 'Article';
        $page->enable_table_of_contents = $request->boolean('enable_table_of_contents');
        $page->enable_prompt_copying = $request->boolean('enable_prompt_copying');

        $page->save();

        return redirect()->back()->with('success_message', 'SEO Landing Page updated successfully.');
    }

    /**
     * Delete landing page and its sections/pivots.
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $page = SeoLandingPage::findOrFail($id);

        if (!empty($page->featured_image)) {
            $file = public_path('uploads/guides/' . $page->featured_image);
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        $page->delete();

        return redirect('panel/admin/seo-pages')->with('success_message', 'Landing Page deleted successfully.');
    }

    /**
     * Store or update a section.
     */
    public function saveSection(Request $request, $pageId)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $page = SeoLandingPage::findOrFail($pageId);

        $request->validate([
            'type' => 'required|string',
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $sectionId = $request->input('section_id');
        $section = $sectionId ? SeoLandingPageSection::where('landing_page_id', $page->id)->findOrFail($sectionId) : new SeoLandingPageSection();

        $section->landing_page_id = $page->id;
        $section->type = $request->type;
        $section->badge = $request->badge;
        $section->heading = $request->heading;
        $section->subheading = $request->subheading;
        $section->content = $request->content ? Purify::clean($request->content) : null;
        $section->settings = $request->settings ?: [];
        $section->sort_order = $request->sort_order ?: ($section->sort_order ?? ($page->sections()->count() + 1));
        $section->is_active = $request->boolean('is_active', true);
        $section->save();

        return response()->json([
            'success' => true,
            'message' => 'Section saved successfully',
            'section' => $section
        ]);
    }

    /**
     * Delete a section.
     */
    public function deleteSection($pageId, $sectionId)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $section = SeoLandingPageSection::where('landing_page_id', $pageId)->findOrFail($sectionId);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Request $request, $pageId)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $orders = $request->input('orders', []);
        foreach ($orders as $index => $sectionId) {
            SeoLandingPageSection::where('landing_page_id', $pageId)
                ->where('id', $sectionId)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Order saved']);
    }

    /**
     * AJAX search prompts for attachment.
     */
    public function searchPrompts(Request $request)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['data' => []]);
        }

        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return response()->json(['data' => []]);
        }

        $prompts = Images::select('id', 'title', 'slug', 'thumbnail', 'prompt', 'ai_model', 'item_for_sale')
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('slug', 'LIKE', "%{$q}%")
                      ->orWhere('prompt', 'LIKE', "%{$q}%")
                      ->orWhere('tags', 'LIKE', "%{$q}%");
            })
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        $data = $prompts->map(function ($img) {
            return [
                'id' => $img->id,
                'title' => $img->title,
                'slug' => $img->slug,
                'thumbnail' => Storage::url(config('path.thumbnail') . $img->thumbnail),
                'ai_model' => $img->ai_model ?: 'General',
                'prompt_snippet' => Str::limit($img->prompt, 100),
                'tier' => $img->item_for_sale === 'free' ? 'Free' : 'Premium'
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * AJAX search photoshoots for attachment.
     */
    public function searchPhotoshoots(Request $request)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['data' => []]);
        }

        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return response()->json(['data' => []]);
        }

        $photoshoots = Photoshoot::select('id', 'title', 'slug', 'prompts_count', 'ai_model')
            ->with(['images' => function ($query) {
                $query->select('id', 'photoshoot_id', 'thumbnail')->take(1);
            }])
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('slug', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%");
            })
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        $data = $photoshoots->map(function ($ps) {
            $firstImg = $ps->images->first();
            return [
                'id' => $ps->id,
                'title' => $ps->title,
                'slug' => $ps->slug,
                'thumbnail' => $firstImg ? Storage::url(config('path.thumbnail') . $firstImg->thumbnail) : url('public/img', config('settings.logo_light', 'logo.png')),
                'prompts_count' => $ps->prompts_count,
                'ai_model' => $ps->ai_model ?: 'Multi-Model'
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Attach an item (prompt/photoshoot) to landing page.
     */
    public function attachItem(Request $request, $pageId)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $page = SeoLandingPage::findOrFail($pageId);

        $request->validate([
            'item_type' => 'required|in:prompt,photoshoot',
            'item_id' => 'required|integer',
            'custom_badge' => 'nullable|string|max:100',
            'custom_title' => 'nullable|string|max:255',
            'custom_notes' => 'nullable|string',
            'section_id' => 'nullable|exists:seo_landing_page_sections,id',
        ]);

        $item = SeoLandingPageItem::updateOrCreate(
            [
                'landing_page_id' => $page->id,
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
            ],
            [
                'section_id' => $request->section_id ?: null,
                'custom_badge' => $request->custom_badge,
                'custom_title' => $request->custom_title,
                'custom_notes' => $request->custom_notes,
                'sort_order' => $page->items()->count() + 1,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item connected to page successfully',
            'item' => $item
        ]);
    }

    /**
     * Detach an item from landing page (safe: master item untouched).
     */
    public function detachItem($pageId, $itemId)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $item = SeoLandingPageItem::where('landing_page_id', $pageId)->findOrFail($itemId);
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item removed from page']);
    }

    /**
     * Retrieve Super Admin authors for dropdown.
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
     * Import or update SEO Landing Page with sections from AI-generated JSON in Draft mode.
     */
    public function importJson(Request $request)
    {
        if (!auth()->user()->hasPermission('seo_pages') && !auth()->user()->hasPermission('full_access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'json_content' => 'required|string',
            'page_id' => 'nullable|integer|exists:seo_landing_pages,id',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $rawJson = trim($request->input('json_content'));

        // Strip UTF-8 BOM if present
        if (substr($rawJson, 0, 3) === "\xEF\xBB\xBF") {
            $rawJson = substr($rawJson, 3);
        }

        // Clean out possible markdown code fences if admin pasted them
        if (Str::startsWith($rawJson, '```json')) {
            $rawJson = Str::after($rawJson, '```json');
        } elseif (Str::startsWith($rawJson, '```')) {
            $rawJson = Str::after($rawJson, '```');
        }
        if (Str::endsWith($rawJson, '```')) {
            $rawJson = Str::beforeLast($rawJson, '```');
        }
        $rawJson = trim($rawJson);

        // 1. Normalize typographic/smart curly quotes to standard ASCII quotes
        $rawJson = str_replace(
            ["\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x98", "\xE2\x80\x99", '“', '”', '‘', '’'],
            ['"', '"', "'", "'", '"', '"', "'", "'"],
            $rawJson
        );

        // Attempt direct json_decode first
        $data = json_decode($rawJson, true);

        // 2. If direct decode failed, attempt cleaning unescaped control characters (newlines, carriage returns, tabs inside strings)
        if (!$data) {
            $sanitized = preg_replace_callback('/"(?:\\\\.|[^"\\\\])*"/s', function ($matches) {
                return str_replace(
                    ["\r\n", "\n", "\r", "\t"],
                    ['\n', '\n', '\n', '\t'],
                    $matches[0]
                );
            }, $rawJson);

            // Strip trailing commas before closing braces/brackets (common LLM syntax issue)
            $sanitized = preg_replace('/,\s*([\}\]])/m', '$1', $sanitized);

            $data = json_decode($sanitized, true);

            // 3. Fallback: filter non-printable control characters
            if (!$data) {
                $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $sanitized);
                $data = json_decode($sanitized, true);
            }
        }

        if (!$data || !is_array($data)) {
            $lastErr = json_last_error_msg();
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON structure: ' . $lastErr . '. Please verify that quotes inside content are escaped (\") and all brackets/commas match.'
            ], 422);
        }

        // Handle either root-level or nested "page" object
        $pageData = isset($data['page']) && is_array($data['page']) ? $data['page'] : $data;
        $sectionsData = isset($data['sections']) && is_array($data['sections']) ? $data['sections'] : [];

        if (empty($pageData['title'])) {
            return response()->json([
                'success' => false,
                'message' => 'The JSON must contain at least a "title" field.'
            ], 422);
        }

        \DB::beginTransaction();
        try {
            $pageId = $request->input('page_id');
            if ($pageId) {
                $page = SeoLandingPage::findOrFail($pageId);
            } else {
                $page = new SeoLandingPage();
                $page->preview_token = Str::random(64);
            }

            // Always enforce draft mode on import
            $page->status = 'draft';

            // User Assignment
            $page->user_id = $request->user_id ?: ($page->user_id ?: auth()->id());

            // Category assignment (from request dropdown or matching category name in page data)
            if ($request->filled('category_id')) {
                $page->category_id = $request->category_id;
            } elseif (!empty($pageData['category'])) {
                $matchedCat = Categories::where('name', 'LIKE', '%' . trim($pageData['category']) . '%')->first();
                if ($matchedCat) {
                    $page->category_id = $matchedCat->id;
                }
            }

            // Core Info
            $page->title = $pageData['title'];
            $page->internal_name = !empty($pageData['internal_name']) ? $pageData['internal_name'] : $pageData['title'];

            // Slug generation / updating
            if (!empty($pageData['slug'])) {
                $slugCandidate = Str::slug($pageData['slug']);
            } else {
                $slugCandidate = Str::slug($pageData['title']);
            }

            if (!$page->exists || ($page->slug !== $slugCandidate)) {
                $slug = $slugCandidate;
                $counter = 1;
                while (SeoLandingPage::where('slug', $slug)->where('id', '!=', $page->id ?? 0)->exists()) {
                    $slug = $slugCandidate . '-' . $counter++;
                }
                $page->slug = $slug;
            }

            $page->excerpt = $pageData['excerpt'] ?? $page->excerpt;
            $page->target_audience = $pageData['target_audience'] ?? $page->target_audience;
            $page->primary_keyword = $pageData['primary_keyword'] ?? $page->primary_keyword;
            $page->secondary_keywords = is_array($pageData['secondary_keywords'] ?? null)
                ? implode(', ', $pageData['secondary_keywords'])
                : ($pageData['secondary_keywords'] ?? $page->secondary_keywords);

            if (!empty($pageData['search_intent']) && in_array($pageData['search_intent'], ['informational', 'commercial', 'transactional'])) {
                $page->search_intent = $pageData['search_intent'];
            } elseif (!$page->search_intent) {
                $page->search_intent = 'informational';
            }

            if (!empty($pageData['featured_image_alt'])) {
                $page->featured_image_alt = $pageData['featured_image_alt'];
            }

            // SEO metadata
            $page->meta_title = $pageData['meta_title'] ?? $page->meta_title;
            $page->meta_description = $pageData['meta_description'] ?? $page->meta_description;
            $page->meta_keywords = is_array($pageData['meta_keywords'] ?? null)
                ? implode(', ', $pageData['meta_keywords'])
                : ($pageData['meta_keywords'] ?? $page->meta_keywords);
            $page->canonical_url = $pageData['canonical_url'] ?? $page->canonical_url;
            $page->robots = $pageData['robots'] ?? ($page->robots ?: 'index, follow');
            $page->og_title = $pageData['og_title'] ?? ($pageData['meta_title'] ?? $page->title);
            $page->og_description = $pageData['og_description'] ?? ($pageData['meta_description'] ?? $page->excerpt);
            $page->schema_type = $pageData['schema_type'] ?? ($page->schema_type ?: 'Article');
            if (isset($pageData['enable_table_of_contents'])) {
                $page->enable_table_of_contents = (bool)$pageData['enable_table_of_contents'];
            }

            $page->save();

            // Process sections if provided in JSON
            if (!empty($sectionsData)) {
                // If updating existing page, replace previous sections or append
                if ($request->boolean('replace_sections', true)) {
                    $page->sections()->delete();
                }

                $sortOrder = 1;
                foreach ($sectionsData as $sec) {
                    $section = new SeoLandingPageSection();
                    $section->landing_page_id = $page->id;
                    $section->type = $sec['type'] ?? 'rich_text';
                    $section->badge = $sec['badge'] ?? null;
                    $section->heading = $sec['heading'] ?? 'Section ' . $sortOrder;
                    $section->subheading = $sec['subheading'] ?? null;
                    $section->content = !empty($sec['content']) ? Purify::clean($sec['content']) : null;
                    $section->settings = $sec['settings'] ?? [];
                    $section->sort_order = $sec['sort_order'] ?? $sortOrder++;
                    $section->is_active = true;
                    $section->save();
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guide imported successfully in Draft mode!',
                'redirect_url' => url('panel/admin/seo-pages/edit/' . $page->id),
                'page' => $page
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import JSON: ' . $e->getMessage()
            ], 500);
        }
    }
}
