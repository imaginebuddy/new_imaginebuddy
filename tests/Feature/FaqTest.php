<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Faq;
use App\Models\User;
use App\Models\SeoMetadata;
use Illuminate\Support\Facades\Cache;

class FaqTest extends TestCase
{
    /**
     * Test /frequently-asked-questions public page loads with status 200.
     */
    public function test_faq_public_page_loads_with_status_200(): void
    {
        $response = $this->get('/frequently-asked-questions');

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('faqSearchInput');
        $response->assertSee('faqAccordion');
    }

    /**
     * Test legacy /faq alias redirects 301 to /frequently-asked-questions.
     */
    public function test_faq_legacy_alias_redirects(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(301);
        $response->assertRedirect('frequently-asked-questions');
    }

    /**
     * Test active FAQs are visible on the public FAQ page.
     */
    public function test_active_faqs_appear_on_public_page(): void
    {
        $faq = Faq::create([
            'question' => 'Unique Active Test Question ' . uniqid(),
            'answer' => 'This is a unique answer for testing active status.',
            'category' => 'Testing',
            'sort_order' => 0,
            'status' => 'active',
        ]);

        try {
            $response = $this->get('/frequently-asked-questions');
            $response->assertStatus(200);
            $response->assertSee($faq->question);
            $response->assertSee('This is a unique answer for testing active status.');
        } finally {
            $faq->delete();
        }
    }

    /**
     * Test inactive / draft FAQs are NOT visible on the public FAQ page.
     */
    public function test_inactive_faqs_do_not_appear_on_public_page(): void
    {
        $draftFaq = Faq::create([
            'question' => 'Secret Inactive Draft Question ' . uniqid(),
            'answer' => 'This secret answer should never be exposed.',
            'category' => 'Drafts',
            'sort_order' => 999,
            'status' => 'inactive',
        ]);

        try {
            $response = $this->get('/frequently-asked-questions');
            $response->assertStatus(200);
            $response->assertDontSee($draftFaq->question);
            $response->assertDontSee('This secret answer should never be exposed.');
        } finally {
            $draftFaq->delete();
        }
    }

    /**
     * Test XML sitemap includes /frequently-asked-questions.
     */
    public function test_sitemap_contains_faq(): void
    {
        $response = $this->get('/sitemaps.xml');

        $response->assertStatus(200);
        $response->assertSee(url('frequently-asked-questions'));
    }

    /**
     * Test FAQ SEO is manageable from Admin settings and renders on /frequently-asked-questions.
     */
    public function test_faq_seo_is_manageable_and_renders(): void
    {
        $seoRecord = SeoMetadata::where('page_key', 'faq')->first();
        $this->assertNotNull($seoRecord, 'FAQ record must exist in seo_metadata table');

        $original = [
            'meta_title' => $seoRecord->meta_title,
            'meta_description' => $seoRecord->meta_description,
            'meta_keywords' => $seoRecord->meta_keywords,
        ];

        try {
            $customTitle = 'Custom Admin FAQ Title - ImagineBuddy';
            $customDesc = 'Custom Admin FAQ Description for search engine testing.';
            $customKeywords = 'faq, custom, seo, test';

            $seoRecord->update([
                'meta_title' => $customTitle,
                'meta_description' => $customDesc,
                'meta_keywords' => $customKeywords,
            ]);
            Cache::forget(SeoMetadata::CACHE_KEY);

            $response = $this->get('/frequently-asked-questions');
            $response->assertStatus(200);
            $response->assertSee('<title>' . $customTitle . '</title>', false);
            $response->assertSee('<meta name="description" content="' . $customDesc . '">', false);
            $response->assertSee('<meta name="keywords" content="' . $customKeywords . '">', false);
        } finally {
            $seoRecord->update($original);
            Cache::forget(SeoMetadata::CACHE_KEY);
        }
    }

    /**
     * Test FAQPage Schema.org JSON-LD structured data is generated.
     */
    public function test_faq_schema_org_json_ld_is_generated(): void
    {
        $response = $this->get('/frequently-asked-questions');

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('application/ld+json', $content);
        $this->assertStringContainsString('"@type": "FAQPage"', $content);
        $this->assertStringContainsString('"@type": "Question"', $content);
        $this->assertStringContainsString('"@type": "Answer"', $content);
    }

    /**
     * Test unauthenticated users cannot access panel/admin/faqs.
     */
    public function test_guest_cannot_access_faq_admin(): void
    {
        $response = $this->get('panel/admin/faqs');

        // Middleware redirects unauthenticated users to login or returns 302/403
        $this->assertTrue(in_array($response->status(), [302, 401, 403]));
    }

    /**
     * Test authorized admin user can view and manage FAQs.
     */
    public function test_admin_can_access_and_manage_faqs(): void
    {
        $admin = User::first();
        if (!$admin) {
            $this->markTestSkipped('No user available for authentication test.');
        }

        // 1. Admin can access listing
        $response = $this->actingAs($admin)->get('panel/admin/faqs');
        $response->assertStatus(200);
        $response->assertSee('FAQs');

        // 2. Admin can view add form
        $addResponse = $this->actingAs($admin)->get('panel/admin/faqs/add');
        $addResponse->assertStatus(200);

        // 3. Admin can store a new FAQ
        $postData = [
            'question' => 'Automated Test Question ' . uniqid(),
            'answer' => '<p>Automated test answer content.</p>',
            'category' => 'Testing',
            'sort_order' => 10,
            'status' => 'active',
        ];

        $storeResponse = $this->actingAs($admin)->post('panel/admin/faqs/add', $postData);
        $storeResponse->assertStatus(302);
        $storeResponse->assertRedirect('panel/admin/faqs');

        $createdFaq = Faq::where('question', $postData['question'])->first();
        $this->assertNotNull($createdFaq);

        // 4. Admin can edit FAQ
        $editResponse = $this->actingAs($admin)->get('panel/admin/faqs/edit/' . $createdFaq->id);
        $editResponse->assertStatus(200);

        // 5. Admin can update FAQ
        $updateResponse = $this->actingAs($admin)->post('panel/admin/faqs/update/' . $createdFaq->id, [
            'question' => 'Updated Automated Question',
            'answer' => '<p>Updated answer content.</p>',
            'category' => 'UpdatedCategory',
            'sort_order' => 5,
            'status' => 'active',
        ]);
        $updateResponse->assertStatus(302);

        $createdFaq->refresh();
        $this->assertEquals('Updated Automated Question', $createdFaq->question);

        // 5b. Admin can deactivate FAQ from edit form (checkbox unchecked = no status in POST)
        $deactivateResponse = $this->actingAs($admin)->post('panel/admin/faqs/update/' . $createdFaq->id, [
            'question' => 'Updated Automated Question',
            'answer' => '<p>Updated answer content.</p>',
            'category' => 'UpdatedCategory',
            'sort_order' => 5,
            // status omitted when unchecked in browser
        ]);
        $deactivateResponse->assertStatus(302);

        $createdFaq->refresh();
        $this->assertEquals('inactive', $createdFaq->status);

        // 6. Admin can toggle status
        $toggleResponse = $this->actingAs($admin)->post('panel/admin/faqs/toggle-status/' . $createdFaq->id);
        $toggleResponse->assertStatus(302);

        $createdFaq->refresh();
        $this->assertEquals('active', $createdFaq->status);

        // 7. Admin can delete FAQ
        $deleteResponse = $this->actingAs($admin)->post('panel/admin/faqs/delete/' . $createdFaq->id);
        $deleteResponse->assertStatus(302);

        $this->assertNull(Faq::find($createdFaq->id));
    }
}
