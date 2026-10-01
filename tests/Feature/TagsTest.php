<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Images;
use App\Models\Query;

class TagsTest extends TestCase
{
    /**
     * Test /categories?tab=tags returns 200 and displays tags.
     */
    public function test_tags_tab_page_returns_200(): void
    {
        $response = $this->get('/categories?tab=tags');

        $response->assertStatus(200);
        $response->assertSee('tagSearchInput');
        $response->assertSee('tagsContainer');
    }

    /**
     * Test /categories?tab=tags AJAX endpoint returns JSON.
     */
    public function test_tags_ajax_pagination_returns_json(): void
    {
        $response = $this->getJson('/categories?tab=tags&page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage'
        ]);
    }

    /**
     * Test /categories?tab=tags AJAX search filtering works.
     */
    public function test_tags_ajax_search_filtering(): void
    {
        $response = $this->getJson('/categories?tab=tags&page=1&q=amazon');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage'
        ]);
        $response->assertSee('Amazon');
    }

    /**
     * Test that Query::tagsImages exact matches tags and does not match substrings of other tags.
     */
    public function test_tags_show_count_matches_exact_tag(): void
    {
        $res = Query::tagsImages('amazon');
        $this->assertEquals(4, $res['total']);

        $resCleanser = Query::tagsImages('cleanser');
        $this->assertEquals(1, $resCleanser['total']);
    }

    /**
     * Test /tags/{slug} detail page loads successfully.
     */
    public function test_tag_detail_page_loads_and_displays_prompts(): void
    {
        $response = $this->get('/tags/amazon');

        $response->assertStatus(200);
        $response->assertSee('Amazon');
        $response->assertSee('Explore 4');
    }

    /**
     * Test /tags/{slug} detail page contains all 4 filter dropdowns.
     */
    public function test_tag_detail_page_has_all_four_filter_dropdowns(): void
    {
        $response = $this->get('/tags/amazon');

        $response->assertStatus(200);
        $response->assertSee('filter-sort');
        $response->assertSee('filter-category');
        $response->assertSee('filter-tier');
        $response->assertSee('filter-ai-model');
        $response->assertSee('All Categories');
        $response->assertSee('All Prompts');
        $response->assertSee('All AI Models');
    }

    /**
     * Test /tags/{slug} detail page filters by category.
     */
    public function test_tag_detail_page_filters_by_category(): void
    {
        $response = $this->get('/tags/amazon?category=beauty-and-skincare');

        $response->assertStatus(200);
        $response->assertSee('Beauty &amp; Skincare', false);
    }

    /**
     * Test /tags/{slug} detail page filters by tier and sort.
     */
    public function test_tag_detail_page_filters_by_tier_and_sort(): void
    {
        $response = $this->get('/tags/amazon?tier=free&sort=oldest');

        $response->assertStatus(200);
        $response->assertSee('Free Prompts');
    }

    /**
     * Test /tags/{slug} AJAX request with filters returns pagination links.
     */
    public function test_tag_detail_page_ajax_returns_html_and_pagination(): void
    {
        $response = $this->get('/tags/amazon?category=beauty-and-skincare&page=1', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
    }
}
