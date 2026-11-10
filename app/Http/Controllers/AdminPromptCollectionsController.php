<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Images;
use App\Models\PromptCollection;
use App\Models\PromptCollectionItem;
use App\Models\PromptPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPromptCollectionsController extends Controller
{
    /**
     * Check permission helper.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->role == 'admin' || $user->role == '1' || !empty($user->is_super_admin)) {
            return;
        }

        if (method_exists($user, 'hasPermission')) {
            if ($user->hasPermission('full_access') || $user->hasPermission('images') || $user->hasPermission('page_manager')) {
                return;
            }
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * List all prompt collections.
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $query = PromptCollection::with(['category', 'items'])->withCount('items');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'LIKE', "%{$q}%")
                   ->orWhere('slug', 'LIKE', "%{$q}%")
                   ->orWhere('summary', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $collections = $query->orderBy('id', 'desc')->paginate(15)->appends($request->query());
        $categories = Categories::where('mode', 'on')->orderBy('name')->get();

        return view('admin.prompts.index', compact('collections', 'categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $this->checkPermission();

        $categories = Categories::where('mode', 'on')->orderBy('name')->get();

        return view('admin.prompts.create', compact('categories'));
    }

    /**
     * Store new collection.
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:prompt_collections,slug',
            'summary' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'ai_engine' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'raw_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:15360',
            'transformed_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:15360',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);
        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while (PromptCollection::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $coverFileName = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $coverFileName = 'cover_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/prompt-collections');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $coverFileName);
        }

        $rawImageName = null;
        if ($request->hasFile('raw_image') && $request->file('raw_image')->isValid()) {
            $rawFile = $request->file('raw_image');
            $rawImageName = 'before_' . time() . '_' . Str::random(8) . '.' . $rawFile->getClientOriginalExtension();
            $destPath = public_path('uploads/prompt-collections/before');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $rawFile->move($destPath, $rawImageName);
        }

        $transformedImageName = null;
        if ($request->hasFile('transformed_image') && $request->file('transformed_image')->isValid()) {
            $transFile = $request->file('transformed_image');
            $transformedImageName = 'after_' . time() . '_' . Str::random(8) . '.' . $transFile->getClientOriginalExtension();
            $destPath = public_path('uploads/prompt-collections/after');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $transFile->move($destPath, $transformedImageName);
        }

        // Process Shortcut Features
        $shortcutFeatures = [];
        if ($request->filled('shortcut_features') && is_array($request->shortcut_features)) {
            foreach ($request->shortcut_features as $feat) {
                if (!empty(trim($feat))) {
                    $shortcutFeatures[] = trim($feat);
                }
            }
        }

        // Process Metrics Items
        $metricsItems = [];
        if ($request->filled('metrics_items') && is_array($request->metrics_items)) {
            foreach ($request->metrics_items as $item) {
                if (!empty($item['stat']) || !empty($item['title'])) {
                    $metricsItems[] = [
                        'stat' => trim($item['stat'] ?? ''),
                        'title' => trim($item['title'] ?? ''),
                        'description' => trim($item['description'] ?? ''),
                    ];
                }
            }
        }

        // Process FAQs
        $faqs = [];
        if ($request->filled('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => trim($faq['question']),
                        'answer' => trim($faq['answer']),
                    ];
                }
            }
        }

        $collection = PromptCollection::create([
            'uuid' => (string) Str::uuid(),
            'title' => $request->title,
            'slug' => $slug,
            'summary' => $request->summary,
            'category_id' => $request->category_id,
            'cover_image' => $coverFileName,
            'raw_image' => $rawImageName,
            'transformed_image' => $transformedImageName,
            'show_shortcut_section' => $request->boolean('show_shortcut_section', true),
            'shortcut_eyebrow' => $request->input('shortcut_eyebrow', 'THE SHORTCUT'),
            'shortcut_title' => $request->shortcut_title,
            'shortcut_description' => $request->shortcut_description,
            'shortcut_features' => $shortcutFeatures,
            'shortcut_cta_text' => $request->shortcut_cta_text,
            'shortcut_cta_url' => $request->shortcut_cta_url,
            'shortcut_subtext' => $request->shortcut_subtext,
            'shortcut_compare_caption' => $request->shortcut_compare_caption,
            'show_metrics_section' => $request->boolean('show_metrics_section', true),
            'metrics_eyebrow' => $request->input('metrics_eyebrow', 'PROVEN IMPACT'),
            'metrics_title' => $request->metrics_title,
            'metrics_subtitle' => $request->metrics_subtitle,
            'metrics_items' => $metricsItems,
            'ai_engine' => $request->ai_engine ?: 'Midjourney & Flux',
            'faqs' => $faqs,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $request->input('status', 'active'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return redirect()->route('admin.prompts.edit', $collection->id)
            ->with('success', 'Prompt Collection created successfully! You can now attach prompts below.');
    }

    /**
     * Show edit form with attached prompts.
     */
    public function edit($id)
    {
        $this->checkPermission();

        $collection = PromptCollection::with(['category', 'items.image.user'])->findOrFail($id);
        $categories = Categories::where('mode', 'on')->orderBy('name')->get();

        return view('admin.prompts.edit', compact('collection', 'categories'));
    }

    /**
     * Update collection details.
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission();

        $collection = PromptCollection::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:prompt_collections,slug,' . $collection->id,
            'summary' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'ai_engine' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'raw_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:15360',
            'transformed_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:15360',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Handle cover banner image removal
        if ($request->boolean('remove_cover_image')) {
            if (!empty($collection->cover_image)) {
                $oldPath = public_path('uploads/prompt-collections/' . $collection->cover_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $collection->cover_image = null;
        }

        if ($request->hasFile('cover_image')) {
            // Delete old file
            if (!empty($collection->cover_image)) {
                $oldPath = public_path('uploads/prompt-collections/' . $collection->cover_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('cover_image');
            $coverFileName = 'cover_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/prompt-collections');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $file->move($destinationPath, $coverFileName);
            $collection->cover_image = $coverFileName;
        }

        // Handle raw (Before) image removal or upload
        if ($request->boolean('remove_raw_image')) {
            if (!empty($collection->raw_image)) {
                $oldPath = public_path('uploads/prompt-collections/before/' . $collection->raw_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $collection->raw_image = null;
        }

        if ($request->hasFile('raw_image') && $request->file('raw_image')->isValid()) {
            if (!empty($collection->raw_image)) {
                $oldPath = public_path('uploads/prompt-collections/before/' . $collection->raw_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $rawFile = $request->file('raw_image');
            $rawImageName = 'before_' . time() . '_' . Str::random(8) . '.' . $rawFile->getClientOriginalExtension();
            $destPath = public_path('uploads/prompt-collections/before');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $rawFile->move($destPath, $rawImageName);
            $collection->raw_image = $rawImageName;
        }

        // Handle transformed (After) image removal or upload
        if ($request->boolean('remove_transformed_image')) {
            if (!empty($collection->transformed_image)) {
                $oldPath = public_path('uploads/prompt-collections/after/' . $collection->transformed_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $collection->transformed_image = null;
        }

        if ($request->hasFile('transformed_image') && $request->file('transformed_image')->isValid()) {
            if (!empty($collection->transformed_image)) {
                $oldPath = public_path('uploads/prompt-collections/after/' . $collection->transformed_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $transFile = $request->file('transformed_image');
            $transformedImageName = 'after_' . time() . '_' . Str::random(8) . '.' . $transFile->getClientOriginalExtension();
            $destPath = public_path('uploads/prompt-collections/after');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $transFile->move($destPath, $transformedImageName);
            $collection->transformed_image = $transformedImageName;
        }

        // Process Shortcut Features
        $shortcutFeatures = [];
        if ($request->filled('shortcut_features') && is_array($request->shortcut_features)) {
            foreach ($request->shortcut_features as $feat) {
                if (!empty(trim($feat))) {
                    $shortcutFeatures[] = trim($feat);
                }
            }
        }

        // Process Metrics Items
        $metricsItems = [];
        if ($request->filled('metrics_items') && is_array($request->metrics_items)) {
            foreach ($request->metrics_items as $item) {
                if (!empty($item['stat']) || !empty($item['title'])) {
                    $metricsItems[] = [
                        'stat' => trim($item['stat'] ?? ''),
                        'title' => trim($item['title'] ?? ''),
                        'description' => trim($item['description'] ?? ''),
                    ];
                }
            }
        }

        // Process FAQs
        $faqs = [];
        if ($request->filled('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => trim($faq['question']),
                        'answer' => trim($faq['answer']),
                    ];
                }
            }
        }

        $collection->title = $request->title;
        $collection->slug = Str::slug($request->slug);
        $collection->summary = $request->summary;
        $collection->category_id = $request->category_id;
        $collection->ai_engine = $request->ai_engine ?: 'Midjourney & Flux';
        $collection->faqs = $faqs;
        $collection->show_shortcut_section = $request->boolean('show_shortcut_section', true);
        $collection->shortcut_eyebrow = $request->input('shortcut_eyebrow', 'THE SHORTCUT');
        $collection->shortcut_title = $request->shortcut_title;
        $collection->shortcut_description = $request->shortcut_description;
        $collection->shortcut_features = $shortcutFeatures;
        $collection->shortcut_cta_text = $request->shortcut_cta_text;
        $collection->shortcut_cta_url = $request->shortcut_cta_url;
        $collection->shortcut_subtext = $request->shortcut_subtext;
        $collection->shortcut_compare_caption = $request->shortcut_compare_caption;
        $collection->show_metrics_section = $request->boolean('show_metrics_section', true);
        $collection->metrics_eyebrow = $request->input('metrics_eyebrow', 'PROVEN IMPACT');
        $collection->metrics_title = $request->metrics_title;
        $collection->metrics_subtitle = $request->metrics_subtitle;
        $collection->metrics_items = $metricsItems;
        $collection->is_featured = $request->boolean('is_featured');
        $collection->status = $request->input('status', 'active');
        $collection->meta_title = $request->meta_title;
        $collection->meta_description = $request->meta_description;
        $collection->meta_keywords = $request->meta_keywords;
        $collection->save();

        return redirect()->back()->with('success', 'Prompt Collection updated successfully!');
    }

    /**
     * Destroy collection.
     */
    public function destroy($id)
    {
        $this->checkPermission();

        $collection = PromptCollection::findOrFail($id);
        $collection->delete();

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt Collection deleted successfully.');
    }

    /**
     * Show and update /prompts hub page settings.
     */
    public function settings()
    {
        $this->checkPermission();

        $settings = PromptPageSetting::getSettings();

        return view('admin.prompts.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'banner_title' => 'nullable|string|max:255',
            'banner_subtitle' => 'nullable|string|max:1000',
            'banner_cta_text' => 'nullable|string|max:100',
            'banner_cta_url' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $settings = PromptPageSetting::first() ?: new PromptPageSetting();

        // Handle cover banner image removal
        if ($request->boolean('remove_cover_image')) {
            if (!empty($settings->cover_image)) {
                $oldPath = public_path('uploads/prompt-collections/' . $settings->cover_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $settings->cover_image = null;
        }

        // Handle cover banner upload
        if ($request->hasFile('cover_image')) {
            if (!empty($settings->cover_image)) {
                $oldPath = public_path('uploads/prompt-collections/' . $settings->cover_image);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('cover_image');
            $fileName = 'hub_cover_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/prompt-collections');
            if (!File::isDirectory($dest)) {
                File::makeDirectory($dest, 0755, true, true);
            }
            $file->move($dest, $fileName);
            $settings->cover_image = $fileName;
        }

        $settings->hero_badge = $request->hero_badge;
        $settings->hero_title = $request->hero_title;
        $settings->hero_subtitle = $request->hero_subtitle;
        $settings->banner_title = $request->banner_title;
        $settings->banner_subtitle = $request->banner_subtitle;
        $settings->banner_cta_text = $request->banner_cta_text;
        $settings->banner_cta_url = $request->banner_cta_url;
        $settings->meta_title = $request->meta_title;
        $settings->meta_description = $request->meta_description;
        $settings->meta_keywords = $request->meta_keywords;
        $settings->save();

        return redirect()->back()->with('success', 'Hub Page Settings updated successfully!');
    }

    // =========================================================
    // AJAX METHODS: MATCHING EDIT-PHOTOSHOOT PATTERN
    // =========================================================

    /**
     * AJAX Search prompts (Images) by title, slug, or ID.
     */
    public function searchPrompts(Request $request)
    {
        $this->checkPermission();

        $q = trim($request->get('q'));
        if (empty($q)) {
            return response()->json(['success' => true, 'prompts' => []]);
        }

        $collectionId = $request->get('collection_id');
        $attachedImageIds = [];
        if ($collectionId) {
            $attachedImageIds = PromptCollectionItem::where('prompt_collection_id', $collectionId)->pluck('image_id')->toArray();
        }

        $prompts = Images::select(['id', 'title', 'slug', 'thumbnail', 'user_id', 'item_for_sale', 'status', 'ai_model'])
            ->with(['user'])
            ->where(function ($sq) use ($q) {
                $sq->where('title', 'LIKE', "%{$q}%")
                   ->orWhere('slug', 'LIKE', "%{$q}%")
                   ->orWhere('id', $q);
            })
            ->take(15)
            ->get()
            ->map(function ($img) use ($attachedImageIds) {
                $thumbUrl = asset('public/img/thumbnail-default.jpg');
                if (!empty($img->thumbnail)) {
                    $thumbUrl = Storage::url(config('path.thumbnail') . $img->thumbnail);
                }

                return [
                    'id' => $img->id,
                    'title' => $img->title,
                    'slug' => $img->slug,
                    'thumb_url' => $thumbUrl,
                    'user_name' => $img->user ? $img->user->username : 'ImagineBuddy',
                    'tier' => $img->item_for_sale == 'sale' ? 'Premium' : 'Free',
                    'status' => $img->status,
                    'ai_model' => $img->ai_model ?: 'General',
                    'is_already_added' => in_array($img->id, $attachedImageIds),
                ];
            });

        return response()->json(['success' => true, 'prompts' => $prompts]);
    }

    /**
     * AJAX Attach a prompt to this collection.
     */
    public function attachPrompt(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'collection_id' => 'required|exists:prompt_collections,id',
            'prompt_id' => 'required|exists:images,id',
            'custom_shot_type' => 'nullable|string|max:100',
        ]);

        $collection = PromptCollection::findOrFail($request->collection_id);
        $image = Images::with('user')->findOrFail($request->prompt_id);

        $exists = PromptCollectionItem::where('prompt_collection_id', $collection->id)
            ->where('image_id', $image->id)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This prompt is already in this collection.'
            ], 422);
        }

        $nextOrder = (PromptCollectionItem::where('prompt_collection_id', $collection->id)->max('sort_order') ?? 0) + 1;

        $item = PromptCollectionItem::create([
            'prompt_collection_id' => $collection->id,
            'image_id' => $image->id,
            'custom_shot_type' => $request->custom_shot_type ?: 'Studio Shot',
            'sort_order' => $nextOrder,
        ]);

        $thumbUrl = asset('public/img/thumbnail-default.jpg');
        if (!empty($image->thumbnail)) {
            $thumbUrl = Storage::url(config('path.thumbnail') . $image->thumbnail);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prompt successfully added to collection.',
            'item' => [
                'id' => $item->id,
                'prompt_id' => $image->id,
                'title' => $image->title,
                'slug' => $image->slug,
                'thumb_url' => $thumbUrl,
                'user_name' => $image->user ? $image->user->username : 'ImagineBuddy',
                'tier' => $image->item_for_sale == 'sale' ? 'Premium' : 'Free',
                'status' => $image->status,
                'custom_shot_type' => $item->custom_shot_type,
                'sort_order' => $item->sort_order,
                'prompt_url' => url('prompt', $image->slug),
            ]
        ]);
    }

    /**
     * AJAX Remove prompt from collection.
     */
    public function removePrompt(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'collection_id' => 'required|exists:prompt_collections,id',
            'prompt_id' => 'required|exists:images,id',
        ]);

        PromptCollectionItem::where('prompt_collection_id', $request->collection_id)
            ->where('image_id', $request->prompt_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prompt removed from collection.'
        ]);
    }

    /**
     * AJAX Reorder prompts in collection.
     */
    public function reorderPrompts(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'collection_id' => 'required|exists:prompt_collections,id',
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($request->order as $position => $imageId) {
            PromptCollectionItem::where('prompt_collection_id', $request->collection_id)
                ->where('image_id', $imageId)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prompt order saved successfully.'
        ]);
    }

    /**
     * AJAX update item shot type or prompt override.
     */
    public function updateItemShotType(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'item_id' => 'required|exists:prompt_collection_items,id',
            'custom_shot_type' => 'nullable|string|max:100',
        ]);

        $item = PromptCollectionItem::findOrFail($request->item_id);
        $item->custom_shot_type = $request->custom_shot_type;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Shot type updated.'
        ]);
    }
}
