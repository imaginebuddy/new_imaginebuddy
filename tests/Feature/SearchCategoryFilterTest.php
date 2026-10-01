<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Categories;
use App\Models\Images;
use App\Models\Query;

class SearchCategoryFilterTest extends TestCase
{
    /**
     * Test /search page contains category filter dropdown with all categories and active categories.
     */
    public function test_search_page_displays_category_dropdown_filter(): void
    {
        $response = $this->get('/search?q=food');

        $response->assertStatus(200);
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
        $response->assertSee('Foods &amp; Drinks', false);
    }

    /**
     * Test search results can be filtered by category slug.
     */
    public function test_search_filters_by_category_slug(): void
    {
        $category = Categories::where('slug', 'foods-and-drinks')->first();
        $this->assertNotNull($category);

        $response = $this->get('/search?q=food&category=foods-and-drinks');

        $response->assertStatus(200);
        $response->assertSee('selected', false);
        $response->assertSee('Foods &amp; Drinks', false);

        // Verify Query::searchImages reflects the filtered count
        request()->merge(['q' => 'food', 'category' => 'foods-and-drinks']);
        $res = Query::searchImages();
        $this->assertEquals(8, $res['total']);
    }

    /**
     * Test search filters by different category.
     */
    public function test_search_filters_by_another_category(): void
    {
        request()->merge(['q' => 'food', 'category' => 'health-and-supplements']);
        $res = Query::searchImages();
        $this->assertEquals(2, $res['total']);
    }

    /**
     * Test searching with category and tier combined.
     */
    public function test_search_combined_category_and_tier_filter(): void
    {
        $response = $this->get('/search?q=food&category=foods-and-drinks&tier=free');

        $response->assertStatus(200);
        $response->assertSee('Free Prompts');
        $response->assertSee('Foods &amp; Drinks', false);
    }

    /**
     * Test search AJAX with category filter.
     */
    public function test_search_ajax_with_category_filter(): void
    {
        $response = $this->get('/search?q=food&category=foods-and-drinks&page=1', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
    }
}
