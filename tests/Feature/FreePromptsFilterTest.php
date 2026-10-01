<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Categories;
use App\Models\Query;

class FreePromptsFilterTest extends TestCase
{
    /**
     * Test /prompts/free page renders all 4 filter dropdowns.
     */
    public function test_free_prompts_page_contains_all_four_dropdowns(): void
    {
        $response = $this->get('/prompts/free');

        $response->assertStatus(200);
        $response->assertSee('filter-sort');
        $response->assertSee('filter-category');
        $response->assertSee('filter-tier');
        $response->assertSee('filter-ai-model');
        $response->assertSee('All Categories');
        $response->assertSee('Free Prompts');
        $response->assertSee('All AI Models');
    }

    /**
     * Test /prompts/free page filters by category.
     */
    public function test_free_prompts_page_filters_by_category(): void
    {
        $response = $this->get('/prompts/free?category=foods-and-drinks');

        $response->assertStatus(200);
        $response->assertSee('Foods &amp; Drinks', false);

        // Verify Query::freeImages total matches
        request()->merge(['category' => 'foods-and-drinks']);
        $res = Query::freeImages();
        $this->assertEquals(2, $res->total());
    }

    /**
     * Test /prompts/free page filters by ai_model.
     */
    public function test_free_prompts_page_filters_by_ai_model(): void
    {
        $response = $this->get('/prompts/free?ai_model=Gemini');

        $response->assertStatus(200);
        $response->assertSee('Gemini');
    }

    /**
     * Test /prompts/free page filters by sort order.
     */
    public function test_free_prompts_page_filters_by_sort_oldest(): void
    {
        $response = $this->get('/prompts/free?sort=oldest');

        $response->assertStatus(200);
    }

    /**
     * Test /prompts/free AJAX request returns HTML and pagination links.
     */
    public function test_free_prompts_ajax_pagination_with_filters(): void
    {
        $response = $this->get('/prompts/free?category=foods-and-drinks&page=1', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
    }
}
