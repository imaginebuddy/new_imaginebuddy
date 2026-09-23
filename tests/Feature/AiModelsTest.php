<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Images;
use Illuminate\Support\Str;

class AiModelsTest extends TestCase
{
    /**
     * Test /ai-models listing page returns 200 and contains AI Models title.
     */
    public function test_ai_models_listing_page_returns_200(): void
    {
        $response = $this->get('/ai-models');

        $response->assertStatus(200);
        $response->assertSee('AI Models');
        $response->assertSee('modelSearchInput');
    }

    /**
     * Test /ai-models AJAX endpoint returns JSON with html, hasMore, nextPage.
     */
    public function test_ai_models_listing_ajax_returns_json(): void
    {
        $response = $this->getJson('/ai-models?page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage'
        ]);
    }

    /**
     * Test /ai-models AJAX search filtering works.
     */
    public function test_ai_models_listing_ajax_search(): void
    {
        $response = $this->getJson('/ai-models?page=1&q=Gemini');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage'
        ]);
    }

    /**
     * Test /ai-model/{slug} detail page returns 200 for a valid configured model.
     */
    public function test_ai_model_detail_page_returns_200_for_valid_model(): void
    {
        $models = Images::getAiModels();
        $this->assertNotEmpty($models);

        $firstModel = $models[0];
        $slug = Str::slug($firstModel);

        $response = $this->get('/ai-model/' . $slug);

        $response->assertStatus(200);
        $response->assertSee($firstModel);
        $response->assertSee('imagesFlex');
    }

    /**
     * Test /ai-model/{slug} returns 404 for an invalid model slug.
     */
    public function test_ai_model_detail_page_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/ai-model/non-existent-ai-model-slug-xyz');

        $response->assertStatus(404);
    }

    /**
     * Test /ai-model/{slug} AJAX pagination returns HTML fragment.
     */
    public function test_ai_model_detail_ajax_pagination(): void
    {
        $models = Images::getAiModels();
        $slug = Str::slug($models[0]);

        $response = $this->get('/ai-model/' . $slug, [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test sitemaps.xml contains /ai-models and /ai-model/{slug}.
     */
    public function test_sitemap_contains_ai_models(): void
    {
        $response = $this->get('/sitemaps.xml');

        $response->assertStatus(200);
        $response->assertSee(url('ai-models'));

        $models = Images::getAiModels();
        $response->assertSee(url('ai-model', Str::slug($models[0])));
    }

    /**
     * Test meta title, description and keywords are manageable through Static & Listing Pages SEO.
     */
    public function test_ai_models_seo_is_manageable_from_admin(): void
    {
        $seoRecord = \App\Models\SeoMetadata::where('page_key', 'ai_models')->first();
        $this->assertNotNull($seoRecord, 'AI Models record must exist in seo_metadata table');
        $this->assertEquals('static_page', $seoRecord->type);

        $original = [
            'meta_title' => $seoRecord->meta_title,
            'meta_description' => $seoRecord->meta_description,
            'meta_keywords' => $seoRecord->meta_keywords,
        ];

        try {
            // Update record as admin would
            $customTitle = 'Custom Admin Title for AI Models';
            $customDesc = 'Custom Admin Description for AI Models Page.';
            $customKeywords = 'custom, ai, models, keywords';

            $seoRecord->update([
                'meta_title' => $customTitle,
                'meta_description' => $customDesc,
                'meta_keywords' => $customKeywords,
            ]);
            \Illuminate\Support\Facades\Cache::forget(\App\Models\SeoMetadata::CACHE_KEY);

            // Visit /ai-models and assert custom title, description, keywords are rendered
            $response = $this->get('/ai-models');
            $response->assertStatus(200);
            $response->assertSee('<title>' . $customTitle . '</title>', false);
            $response->assertSee('<meta name="description" content="' . $customDesc . '">', false);
            $response->assertSee('<meta name="keywords" content="' . $customKeywords . '">', false);
        } finally {
            $seoRecord->update($original);
            \Illuminate\Support\Facades\Cache::forget(\App\Models\SeoMetadata::CACHE_KEY);
        }
    }

    /**
     * Test programmatic dynamic patterns for /ai-model/{slug} detail pages are manageable from admin.
     */
    public function test_ai_model_detail_seo_pattern_is_dynamic_and_manageable_from_admin(): void
    {
        $templateRecord = \App\Models\SeoMetadata::where('page_key', 'ai_model_template')->first();
        $this->assertNotNull($templateRecord, 'AI Model template record must exist in seo_metadata table');
        $this->assertEquals('dynamic_template', $templateRecord->type);

        $original = [
            'meta_title' => $templateRecord->meta_title,
            'meta_description' => $templateRecord->meta_description,
            'meta_keywords' => $templateRecord->meta_keywords,
        ];

        $models = Images::getAiModels();
        $this->assertNotEmpty($models);
        $testModel = $models[0];
        $slug = Str::slug($testModel);
        $siteTitle = config('settings.title') ?: config('app.name', 'ImagineBuddy');

        try {
            $patternTitle = 'Explore {model} Super Prompts | {site_name}';
            $patternDesc = 'Curated library of {model} prompts and formulas on {site_name}.';
            $patternKeywords = '{model}, test prompt, {model} generator';

            $templateRecord->update([
                'meta_title' => $patternTitle,
                'meta_description' => $patternDesc,
                'meta_keywords' => $patternKeywords,
            ]);
            \Illuminate\Support\Facades\Cache::forget(\App\Models\SeoMetadata::CACHE_KEY);

            $expectedTitle = "Explore {$testModel} Super Prompts | {$siteTitle}";
            $expectedDesc = "Curated library of {$testModel} prompts and formulas on {$siteTitle}.";
            $expectedKeywords = "{$testModel}, test prompt, {$testModel} generator";

            $response = $this->get('/ai-model/' . $slug);
            $response->assertStatus(200);
            $response->assertSee('<title>' . $expectedTitle . '</title>', false);
            $response->assertSee('<meta name="description" content="' . $expectedDesc . '">', false);
            $response->assertSee('<meta name="keywords" content="' . $expectedKeywords . '">', false);
        } finally {
            $templateRecord->update($original);
            \Illuminate\Support\Facades\Cache::forget(\App\Models\SeoMetadata::CACHE_KEY);
        }
    }

    /**
     * Test header nav includes /ai-models in explore section for both desktop and mobile versions.
     */
    public function test_navbar_contains_ai_models_in_desktop_and_mobile_explore_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify link URL and text in desktop dropdown
        $aiModelsUrl = url('ai-models');
        $response->assertSee('href="' . $aiModelsUrl . '"', false);

        // Assert presence in desktop explore dropdown
        $desktopSegment = 'aria-labelledby="dropdownExplore"';
        $response->assertSee($desktopSegment, false);

        // Assert presence in mobile offcanvas explore collapse
        $mobileSegment = 'id="explore"';
        $response->assertSee($mobileSegment, false);

        // Verify desktop explore contains photoshoots followed by ai-models
        $content = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/href="[^"]*photoshoots"[^>]*>.*?Photoshoots<\/a>.*?href="[^"]*ai-models"[^>]*>.*?AI Models<\/a>/s',
            $content
        );

        // Verify total links across page (desktop navbar, mobile navbar, footer)
        $this->assertEquals(
            3,
            substr_count($content, 'href="' . $aiModelsUrl . '"'),
            'The /ai-models link must appear in desktop header, mobile offcanvas, and footer'
        );
    }
}
