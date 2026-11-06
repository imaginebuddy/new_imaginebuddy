<?php

namespace App\Http\Controllers;

use App\Models\WebsitePageSetting;
use App\Models\AiModelContent;
use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminWebsitePageContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkPermission()
    {
        if (
            auth()->user()->isSuperAdmin() ||
            auth()->user()->hasPermission('page_manager') ||
            auth()->user()->hasPermission('seo_pages') ||
            auth()->user()->hasPermission('general_settings') ||
            auth()->user()->hasPermission('full_access')
        ) {
            return;
        }
        abort(403, 'Unauthorized access.');
    }

    /**
     * Show main settings interface
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $activeTab = $request->get('tab', 'ai_models');

        $pageKeys = [
            'ai_models',
            'photoshoots',
            'prompts_free',
            'prompts_premium',
            'popular',
            'latest',
            'most_viewed',
            'most_downloads',
            'most_copied',
            'pricing',
            'marketplace',
            'use_case',
            'industry',
            'product',
            'garment',
            'platform_ai_ads',
        ];

        $pages = [];
        foreach ($pageKeys as $key) {
            $pages[$key] = WebsitePageSetting::getByKey($key);
        }

        // Prepare configured and active AI Models
        $configuredModels = Images::getAiModels();
        $dbCounts = Images::where('status', 'active')
            ->whereNotNull('ai_model')
            ->where('ai_model', '!=', '')
            ->select('ai_model', \DB::raw('count(*) as total'))
            ->groupBy('ai_model')
            ->pluck('total', 'ai_model')
            ->toArray();

        $countsMap = [];
        foreach ($dbCounts as $mName => $c) {
            $countsMap[strtolower(trim($mName))] = $c;
        }

        $allModelsList = [];
        $seenSlugs = [];

        foreach ($configuredModels as $modelName) {
            $slug = Str::slug($modelName);
            if (isset($seenSlugs[$slug])) continue;
            $seenSlugs[$slug] = true;

            $count = $countsMap[strtolower(trim($modelName))] ?? 0;
            $customContent = AiModelContent::where('slug', $slug)->first();

            $allModelsList[] = [
                'name' => $modelName,
                'slug' => $slug,
                'count' => $count,
                'is_customized' => $customContent ? true : false,
                'page_title' => $customContent->page_title ?? null,
                'hero_lead' => $customContent->hero_lead ?? null,
            ];
        }

        // Also include any models in database not explicitly in settings
        foreach ($dbCounts as $mName => $count) {
            $slug = Str::slug($mName);
            if (isset($seenSlugs[$slug]) || empty($slug)) continue;
            $seenSlugs[$slug] = true;

            $customContent = AiModelContent::where('slug', $slug)->first();

            $allModelsList[] = [
                'name' => $mName,
                'slug' => $slug,
                'count' => $count,
                'is_customized' => $customContent ? true : false,
                'page_title' => $customContent->page_title ?? null,
                'hero_lead' => $customContent->hero_lead ?? null,
            ];
        }

        usort($allModelsList, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return view('admin.website-pages-content', compact('activeTab', 'pages', 'allModelsList'));
    }

    /**
     * Save settings for a fixed page
     */
    public function updatePage(Request $request, string $key)
    {
        $this->checkPermission();

        $key = str_replace('-', '_', $key);

        $allowedKeys = [
            'ai_models',
            'photoshoots',
            'prompts_free',
            'prompts_premium',
            'popular',
            'latest',
            'most_viewed',
            'most_downloads',
            'most_copied',
            'pricing',
            'marketplace',
            'use_case',
            'industry',
            'product',
            'garment',
            'platform_ai_ads',
        ];

        if (!in_array($key, $allowedKeys)) {
            abort(404, 'Invalid page key.');
        }

        $validated = $request->validate([
            'page_title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:2000',
            'meta_keywords' => 'nullable|string|max:1000',
            'extra_data' => 'nullable|array',
        ]);

        WebsitePageSetting::updateOrCreate(
            ['page_key' => $key],
            [
                'page_title' => $validated['page_title'] ?? null,
                'subtitle' => $validated['subtitle'] ?? null,
                'seo_title' => $validated['seo_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
                'extra_data' => $validated['extra_data'] ?? [],
            ]
        );

        return redirect()->to(url('panel/admin/website-pages-content') . '?tab=' . $key)
            ->with('success_message', 'Settings for ' . Str::headline(str_replace('_', ' ', $key)) . ' updated successfully!');
    }

    /**
     * Edit form for individual AI Model detail content
     */
    public function editAiModel(Request $request, string $slug)
    {
        $this->checkPermission();

        $configuredModels = Images::getAiModels();
        $matchedName = null;
        foreach ($configuredModels as $m) {
            if (Str::slug($m) === $slug) {
                $matchedName = $m;
                break;
            }
        }

        if (!$matchedName) {
            $matchedName = Str::headline(str_replace('-', ' ', $slug));
        }

        $content = AiModelContent::getBySlug($slug, $matchedName);
        $totalPrompts = Images::where('status', 'active')
            ->where('ai_model', $matchedName)
            ->count();

        return view('admin.edit-ai-model-content', compact('content', 'slug', 'matchedName', 'totalPrompts'));
    }

    /**
     * Update individual AI Model detail content
     */
    public function updateAiModel(Request $request, string $slug)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'model_name' => 'required|string|max:100',
            'page_title' => 'nullable|string',
            'hero_lead' => 'nullable|string|max:2000',
            'seo_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:2000',
            'meta_keywords' => 'nullable|string|max:1000',
        ]);

        AiModelContent::updateOrCreate(
            ['slug' => $slug],
            [
                'model_name' => $validated['model_name'],
                'page_title' => $validated['page_title'] ?? null,
                'hero_lead' => $validated['hero_lead'] ?? null,
                'seo_title' => $validated['seo_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]
        );

        return redirect()->to(url('panel/admin/website-pages-content') . '?tab=ai_model_detail')
            ->with('success_message', 'Content for AI Model "' . $validated['model_name'] . '" updated successfully!');
    }
}
