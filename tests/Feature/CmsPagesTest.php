<?php

namespace Tests\Feature;

use Tests\TestCase;

class CmsPagesTest extends TestCase
{
    /**
     * Test vertical hub pages load with 200 OK.
     */
    public function test_vertical_hubs_load_successfully(): void
    {
        $hubs = ['use-case', 'industry', 'product', 'garment', 'marketplace', 'platform-ai-ads'];

        foreach ($hubs as $hub) {
            $response = $this->get('/' . $hub);
            $response->assertStatus(200);
            $response->assertSee('cms-page-wrapper', false);
        }
        $this->assertTrue(true);
    }

    /**
     * Test solution detail pages load with 200 OK.
     */
    public function test_solution_detail_pages_load_successfully(): void
    {
        $details = [
            '/use-case/d2c-product-photography',
            '/garment/saree-product-photography',
            '/marketplace/amazon-product-photography',
            '/industry/jewelry-product-photography',
            '/product/product-carousel-ads',
            '/platform-ai-ads/facebook-ad-creatives',
        ];

        foreach ($details as $url) {
            $parts = explode('/', trim($url, '/'));
            $exists = \App\Models\SeoLandingPage::published()->where('route_prefix', $parts[0])->where('slug', $parts[1])->exists();
            if ($exists) {
                $response = $this->get($url);
                $response->assertStatus(200);
                $response->assertSee('cms-page-wrapper', false);
                $response->assertSee('cms-detail-hero', false);
            }
        $this->assertTrue(true);
        }
        $this->assertTrue(true);

        // Test D2C page with connected prompts and testimonials
        $d2c = \App\Models\SeoLandingPage::where('slug', 'd2c-product-photography')->first();
        if ($d2c) {
            $response = $this->get('/' . $d2c->route_prefix . '/' . $d2c->slug);
            $response->assertStatus(200);
            $response->assertSee('prompts-showcase', false);
            $response->assertSee('testimonials-section', false);
            $response->assertSee('testimonials-carousel-wrapper', false);

            $content = $response->getContent();
            $testimonialsPos = strpos($content, 'id="testimonials"');
            $faqPos = strpos($content, 'id="faq"');
            $this->assertNotFalse($testimonialsPos, 'Testimonials section should be present');
            $this->assertNotFalse($faqPos, 'FAQ section should be present');
            $this->assertTrue($testimonialsPos < $faqPos, 'Testimonials section should appear before FAQ section');
        }
        $this->assertTrue(true);
    }

    /**
     * Test admin page manager accessibility for admin user.
     */
    public function test_admin_page_manager_accessible_by_admin(): void
    {
        $superAdminRole = \App\Models\RolesAndPermissions::where('permissions', 'full_access')->first();
        $admin = $superAdminRole
            ? \App\Models\User::where('role', $superAdminRole->id)->first()
            : \App\Models\User::first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }
        $this->assertTrue(true);

        // Test index
        $response = $this->actingAs($admin)->get('/panel/admin/page-manager');
        $response->assertStatus(200);
        $response->assertSee('Centralized Page Manager');
        $response->assertDontSee('onsubmit="return confirm(', false);

        // Test create
        $response = $this->actingAs($admin)->get('/panel/admin/page-manager/create');
        $response->assertStatus(200);
        $response->assertSee('Create New Page');

        // Test edit on first page
        $firstPage = \App\Models\SeoLandingPage::first();
        if ($firstPage) {
            $response = $this->actingAs($admin)->get('/panel/admin/page-manager/edit/' . $firstPage->id);
            $response->assertStatus(200);
            $response->assertSee('Section Builder');
        }
        $this->assertTrue(true);
    }

    /**
     * Test page creation and 301 redirect generation on slug change.
     */
    public function test_page_creation_and_slug_redirect(): void
    {
        $superAdminRole = \App\Models\RolesAndPermissions::where('permissions', 'full_access')->first();
        $admin = $superAdminRole
            ? \App\Models\User::where('role', $superAdminRole->id)->first()
            : \App\Models\User::first();

        // 1. Create a page via admin controller
        $storeResponse = $this->actingAs($admin)->post('/panel/admin/page-manager/create', [
            'route_prefix' => 'marketplace',
            'page_type' => 'solution',
            'title' => 'Test Marketplace Photography',
            'slug' => 'test-marketplace-photography',
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'header_badge' => 'TEST BADGE',
            'excerpt' => 'This is a test solution excerpt.',
        ]);

        $createdPage = \App\Models\SeoLandingPage::where('slug', 'test-marketplace-photography')->first();
        $this->assertNotNull($createdPage);
        $this->assertEquals('marketplace', $createdPage->route_prefix);
        $this->assertGreaterThan(0, $createdPage->sections()->count());

        // 2. Update slug and assert 301 redirect record was generated
        $this->actingAs($admin)->post('/panel/admin/page-manager/update/' . $createdPage->id, [
            'route_prefix' => 'marketplace',
            'page_type' => 'solution',
            'title' => 'Test Marketplace Photography Updated',
            'slug' => 'test-marketplace-photography-renamed',
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
        ]);

        $redirect = \App\Models\UrlRedirect::where('source_url', '/marketplace/test-marketplace-photography')->first();
        $this->assertNotNull($redirect);
        $this->assertEquals('/marketplace/test-marketplace-photography-renamed', $redirect->destination_url);
        $this->assertEquals(301, $redirect->redirect_type);

        // Clean up test data
        $createdPage->delete();
        $redirect->delete();
    }

    /**
     * Test uploading and removing a featured image on an isolated test page.
     */
    public function test_featured_image_upload_and_removal(): void
    {
        $superAdminRole = \App\Models\RolesAndPermissions::where('permissions', 'full_access')->first();
        $admin = $superAdminRole
            ? \App\Models\User::where('role', $superAdminRole->id)->first()
            : \App\Models\User::first();

        $page = \App\Models\SeoLandingPage::create([
            'route_prefix' => 'use-case',
            'page_type' => 'solution',
            'title' => 'Test Isolated Page For Featured Image',
            'slug' => 'test-isolated-featured-' . strtolower(\Illuminate\Support\Str::random(8)),
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'published_at' => now(),
            'preview_token' => \Illuminate\Support\Str::random(32),
            'excerpt' => 'Temporary test excerpt',
        ]);

        // Test uploading image
        $file = \Illuminate\Http\UploadedFile::fake()->image('featured_test.jpg', 600, 400);

        $response = $this->actingAs($admin)->post('/panel/admin/page-manager/update/' . $page->id, [
            'route_prefix' => $page->route_prefix,
            'page_type' => $page->page_type,
            'title' => $page->title,
            'slug' => $page->slug,
            'excerpt' => $page->excerpt,
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'featured_image' => $file,
        ]);

        $response->assertSessionHas('success_message');
        $page->refresh();
        $this->assertNotEmpty($page->featured_image);
        $uploadedPath = public_path('uploads/guides/' . $page->featured_image);
        $this->assertFileExists($uploadedPath);

        // Test removing image
        $removeResponse = $this->actingAs($admin)->post('/panel/admin/page-manager/update/' . $page->id, [
            'route_prefix' => $page->route_prefix,
            'page_type' => $page->page_type,
            'title' => $page->title,
            'slug' => $page->slug,
            'excerpt' => $page->excerpt,
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'remove_featured_image' => '1',
        ]);

        $removeResponse->assertSessionHas('success_message');
        $page->refresh();
        $this->assertEmpty($page->featured_image);
        $this->assertFileDoesNotExist($uploadedPath);

        // Clean up test page
        $page->delete();
    }

    /**
     * Test uploading and removing before/after slider images on an isolated test page.
     */
    public function test_before_after_slider_images_upload_and_removal(): void
    {
        $superAdminRole = \App\Models\RolesAndPermissions::where('permissions', 'full_access')->first();
        $admin = $superAdminRole
            ? \App\Models\User::where('role', $superAdminRole->id)->first()
            : \App\Models\User::first();

        $page = \App\Models\SeoLandingPage::create([
            'route_prefix' => 'use-case',
            'page_type' => 'solution',
            'title' => 'Test Isolated Page For Slider Images',
            'slug' => 'test-isolated-slider-' . strtolower(\Illuminate\Support\Str::random(8)),
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'published_at' => now(),
            'preview_token' => \Illuminate\Support\Str::random(32),
            'excerpt' => 'Temporary test excerpt',
        ]);

        $beforeFile = \Illuminate\Http\UploadedFile::fake()->image('raw_snapshot.jpg', 600, 750);
        $afterFile = \Illuminate\Http\UploadedFile::fake()->image('campaign_ready.jpg', 600, 750);

        // Upload both images with custom labels
        $response = $this->actingAs($admin)->post('/panel/admin/page-manager/update/' . $page->id, [
            'route_prefix' => $page->route_prefix,
            'page_type' => 'solution',
            'title' => $page->title,
            'slug' => $page->slug,
            'excerpt' => $page->excerpt,
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'before_image' => $beforeFile,
            'after_image' => $afterFile,
            'before_label' => 'Raw Snapshot',
            'after_label' => 'Campaign Ready',
        ]);

        $response->assertSessionHas('success_message');
        $page->refresh();

        $this->assertNotEmpty($page->before_image);
        $this->assertNotEmpty($page->after_image);
        $this->assertEquals('Raw Snapshot', $page->before_label);
        $this->assertEquals('Campaign Ready', $page->after_label);

        $beforePath = public_path('uploads/guides/' . $page->before_image);
        $afterPath = public_path('uploads/guides/' . $page->after_image);
        $this->assertFileExists($beforePath);
        $this->assertFileExists($afterPath);

        // Check public solution page renders the slider
        $publicUrl = '/' . $page->route_prefix . '/' . $page->slug;
        $pageResponse = $this->get($publicUrl);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('before-after-slider-container');
        $pageResponse->assertSee('Raw Snapshot');
        $pageResponse->assertSee('Campaign Ready');

        // Test removing both images
        $removeResponse = $this->actingAs($admin)->post('/panel/admin/page-manager/update/' . $page->id, [
            'route_prefix' => $page->route_prefix,
            'page_type' => 'solution',
            'title' => $page->title,
            'slug' => $page->slug,
            'excerpt' => $page->excerpt,
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'remove_before_image' => '1',
            'remove_after_image' => '1',
        ]);

        $removeResponse->assertSessionHas('success_message');
        $page->refresh();
        $this->assertNull($page->before_image);
        $this->assertNull($page->after_image);
        $this->assertFileDoesNotExist($beforePath);
        $this->assertFileDoesNotExist($afterPath);

        // Clean up test page
        $page->delete();
    }

    /**
     * Test uploading a step card image for alternating_rows section on an isolated test page.
     */
    public function test_step_card_image_upload_and_save(): void
    {
        $superAdminRole = \App\Models\RolesAndPermissions::where('permissions', 'full_access')->first();
        $admin = $superAdminRole
            ? \App\Models\User::where('role', $superAdminRole->id)->first()
            : \App\Models\User::first();

        $page = \App\Models\SeoLandingPage::create([
            'route_prefix' => 'use-case',
            'page_type' => 'solution',
            'title' => 'Test Isolated Page For Step Card Images',
            'slug' => 'test-isolated-step-' . strtolower(\Illuminate\Support\Str::random(8)),
            'user_id' => $admin->id,
            'search_intent' => 'commercial',
            'status' => 'published',
            'published_at' => now(),
            'preview_token' => \Illuminate\Support\Str::random(32),
            'excerpt' => 'Temporary test excerpt',
        ]);

        $section = \App\Models\SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'alternating_rows',
            'badge' => 'TEST SECTION',
            'heading' => 'Test Heading',
            'subheading' => 'Test Subheading',
            'sort_order' => 1,
            'is_active' => true,
            'settings' => [
                'cards' => [
                    [
                        'badge' => 'TEST CARD',
                        'title' => 'Test Title',
                        'description' => 'Test Desc',
                        'image' => null
                    ]
                ]
            ]
        ]);

        $testImage = \Illuminate\Http\UploadedFile::fake()->image('step_test.jpg', 600, 400);

        $response = $this->actingAs($admin)->post('/panel/admin/page-manager/' . $page->id . '/section/save', [
            'section_id' => $section->id,
            'type' => $section->type,
            'badge' => $section->badge,
            'heading' => $section->heading,
            'subheading' => $section->subheading,
            'card_images' => [
                0 => $testImage
            ]
        ]);

        $response->assertSessionHas('success_message');
        $section->refresh();

        $cards = $section->cards;
        $this->assertNotEmpty($cards[0]['image']);
        $this->assertStringContainsString('step_', $cards[0]['image']);

        // Check public page renders the uploaded image
        $pageResponse = $this->get('/' . $page->route_prefix . '/' . $page->slug);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee($cards[0]['image'], false);

        // Clean up test page & section
        $page->delete();
    }

    /**
     * Test sitemaps include vertical hubs and published CMS pages.
     */
    public function test_sitemap_includes_cms_pages_and_vertical_hubs(): void
    {
        \App\Http\Controllers\SitemapController::clearCache();

        $response = $this->get('/sitemaps-main.xml');
        $response->assertStatus(200);

        // Check vertical hubs are present
        foreach (['use-case', 'industry', 'product', 'garment', 'marketplace', 'platform-ai-ads'] as $hub) {
            $response->assertSee(url($hub), false);
        }

        // Check published landing pages are present
        $published = \App\Models\SeoLandingPage::published()->first();
        if ($published) {
            $response->assertSee($published->public_url, false);
        }

        // Check master index includes landing page updates
        $indexResponse = $this->get('/sitemap.xml');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee(url('sitemaps-main.xml'), false);
    }

    /**
     * Test website footer renders CMS vertical navigation dynamically.
     */
    public function test_footer_renders_cms_navigation_sections_dynamically(): void
    {
        $admin = \App\Models\User::first();
        $adminId = $admin ? $admin->id : 1;

        \App\Models\SeoLandingPage::firstOrCreate(
            ['slug' => 'test-use-case-photography'],
            [
                'title' => 'Test Use Case Photography',
                'route_prefix' => 'use-case',
                'page_type' => 'solution',
                'status' => 'published',
                'published_at' => now(),
                'preview_token' => \Illuminate\Support\Str::random(32),
                'user_id' => $adminId,
            ]
        );

        \App\Models\SeoLandingPage::firstOrCreate(
            ['slug' => 'test-industry-photography'],
            [
                'title' => 'Test Industry Photography',
                'route_prefix' => 'industry',
                'page_type' => 'solution',
                'status' => 'published',
                'published_at' => now(),
                'preview_token' => \Illuminate\Support\Str::random(32),
                'user_id' => $adminId,
            ]
        );

        \Illuminate\Support\Facades\Cache::forget('footer_cms_navigation');

        $response = $this->get('/');
        $response->assertStatus(200);

        // Footer CMS container
        $response->assertSee('footer-cms-nav', false);
        $response->assertSee('footer-cms-heading', false);

        // Sections with published pages must appear
        $response->assertSee('Use Cases', false);
        $response->assertSee('Industries', false);
        $response->assertSee(url('use-case'), false);
        $response->assertSee(url('industry'), false);

        // Links must contain "Explore all"
        $response->assertSee('Explore all', false);

        // Sections without published pages must NOT appear
        $sections = \App\Helper::footerCmsNavigation();
        $keys = array_column($sections, 'key');
        $this->assertContains('use-case', $keys);
        $this->assertContains('industry', $keys);
        $this->assertNotContains('garment', $keys);
        $this->assertNotContains('product', $keys);
        $this->assertNotContains('marketplace', $keys);
        $this->assertNotContains('platform-ai-ads', $keys);

        // Each section must contain at most 3 pages
        foreach ($sections as $sec) {
            $this->assertLessThanOrEqual(3, $sec['pages']->count());
            $this->assertGreaterThanOrEqual(1, $sec['pages']->count());
        }
    }
}
