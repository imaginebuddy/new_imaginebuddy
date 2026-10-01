<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Categories;
use App\Models\Query;

class ExploreCategoryFilterTest extends TestCase
{
    /**
     * Test /latest page contains category filter dropdown.
     */
    public function test_latest_page_contains_category_filter(): void
    {
        $response = $this->get('/latest');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
        $response->assertSee('filter-tier');
        $response->assertSee('filter-ai-model');
    }

    /**
     * Test /featured page contains category filter dropdown.
     */
    public function test_featured_page_contains_category_filter(): void
    {
        $response = $this->get('/featured');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
        $response->assertSee('filter-timeframe');
    }

    /**
     * Test /popular page contains category filter dropdown.
     */
    public function test_popular_page_contains_category_filter(): void
    {
        $response = $this->get('/popular');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
    }

    /**
     * Test /most/viewed page contains category filter dropdown.
     */
    public function test_viewed_page_contains_category_filter(): void
    {
        $response = $this->get('/most/viewed');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
    }

    /**
     * Test /most/downloads page contains category filter dropdown.
     */
    public function test_downloads_page_contains_category_filter(): void
    {
        $response = $this->get('/most/downloads');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
    }

    /**
     * Test /most/copied page contains category filter dropdown.
     */
    public function test_copied_page_contains_category_filter(): void
    {
        $response = $this->get('/most/copied');

        $response->assertStatus(200);
        $response->assertSee('filter-explore');
        $response->assertSee('filter-category');
        $response->assertSee('All Categories');
    }

    /**
     * Test /latest filters by category slug.
     */
    public function test_latest_filters_by_category(): void
    {
        $response = $this->get('/latest?category=foods-and-drinks');

        $response->assertStatus(200);
        $response->assertSee('Foods &amp; Drinks', false);

        $cat = Categories::where('slug', 'foods-and-drinks')->first();
        request()->merge(['category' => 'foods-and-drinks']);
        $images = Query::latestImages();
        $this->assertGreaterThan(0, $images->total());
        foreach ($images as $img) {
            $this->assertEquals($cat->id, $img->categories_id);
        }
    }

    /**
     * Test /latest with non-existent category displays empty state with filter bar intact.
     */
    public function test_latest_with_non_existent_category_shows_filter(): void
    {
        $response = $this->get('/latest?category=non-existent-category-slug');

        $response->assertStatus(200);
        $response->assertSee('filter-category');
        $response->assertSee('filter-explore');
    }

    /**
     * Test /latest AJAX pagination request.
     */
    public function test_latest_ajax_pagination_returns_links(): void
    {
        $response = $this->get('/latest?category=foods-and-drinks&page=1', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
    }
}
