<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Analytics\AnalyticsSearch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchAnalyticsTest extends TestCase
{
    /**
     * Test admin can access analytics searches page.
     */
    public function test_admin_can_access_analytics_searches(): void
    {
        $admin = User::whereNotNull('role')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $response = $this->actingAs($admin)->get('panel/admin/analytics/searches');

        $response->assertStatus(200);
        $response->assertSee('Most Searched Queries');
        $response->assertSee('Last Searched');
    }

    /**
     * Test most searched queries are ordered descending by last_searched.
     */
    public function test_searches_are_sorted_descending_by_latest_timestamp(): void
    {
        $admin = User::whereNotNull('role')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $uniqueKey = 'test_sort_' . time();
        $now = Carbon::now();

        // Create older search
        $olderSearch = AnalyticsSearch::create([
            'session_id' => 'sess_' . $uniqueKey . '_old',
            'visitor_id' => 'vis_' . $uniqueKey . '_old',
            'query' => $uniqueKey . '_older',
            'results_count' => 10,
            'created_at' => $now->copy()->subHours(2),
        ]);

        // Create newer search
        $newerSearch = AnalyticsSearch::create([
            'session_id' => 'sess_' . $uniqueKey . '_new',
            'visitor_id' => 'vis_' . $uniqueKey . '_new',
            'query' => $uniqueKey . '_newer',
            'results_count' => 5,
            'created_at' => $now->copy()->subMinutes(5),
        ]);

        try {
            $response = $this->actingAs($admin)->get("panel/admin/analytics/searches?q={$uniqueKey}&range=today");

            $response->assertStatus(200);

            $topQueries = $response->viewData('topQueries');
            $this->assertNotNull($topQueries);
            $this->assertGreaterThanOrEqual(2, $topQueries->count());

            $first = $topQueries->first();
            $this->assertEquals($uniqueKey . '_newer', $first->query);

            $prevTime = null;
            foreach ($topQueries as $item) {
                if ($prevTime !== null) {
                    $this->assertTrue(
                        Carbon::parse($item->last_searched)->lte(Carbon::parse($prevTime)),
                        "Expected {$item->last_searched} to be <= {$prevTime}"
                    );
                }
                $prevTime = $item->last_searched;
            }
        } finally {
            AnalyticsSearch::where('query', 'LIKE', "{$uniqueKey}%")->delete();
        }
    }

    /**
     * Test re-searching an older query moves it to the top.
     */
    public function test_re_searching_historical_query_moves_it_to_top(): void
    {
        $admin = User::whereNotNull('role')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $uniqueKey = 'test_re_' . time();
        $now = Carbon::now();

        // Query A created 3 hours ago
        AnalyticsSearch::create([
            'session_id' => 'sess_' . $uniqueKey . '_a',
            'visitor_id' => 'vis_' . $uniqueKey . '_a',
            'query' => $uniqueKey . '_alpha',
            'results_count' => 10,
            'created_at' => $now->copy()->subHours(3),
        ]);

        // Query B created 1 hour ago
        AnalyticsSearch::create([
            'session_id' => 'sess_' . $uniqueKey . '_b',
            'visitor_id' => 'vis_' . $uniqueKey . '_b',
            'query' => $uniqueKey . '_beta',
            'results_count' => 8,
            'created_at' => $now->copy()->subHours(1),
        ]);

        try {
            // Before re-search: Query B is newer than Query A
            $response = $this->actingAs($admin)->get("panel/admin/analytics/searches?q={$uniqueKey}&range=today");
            $response->assertStatus(200);
            $topQueries = $response->viewData('topQueries');
            $this->assertEquals($uniqueKey . '_beta', $topQueries->first()->query);

            // Now re-search Query A with current timestamp
            AnalyticsSearch::create([
                'session_id' => 'sess_' . $uniqueKey . '_a2',
                'visitor_id' => 'vis_' . $uniqueKey . '_a2',
                'query' => $uniqueKey . '_alpha',
                'results_count' => 12,
                'created_at' => Carbon::now(),
            ]);

            // After re-search: Query A should now be at the top
            $responseAfter = $this->actingAs($admin)->get("panel/admin/analytics/searches?q={$uniqueKey}&range=today");
            $responseAfter->assertStatus(200);
            $topQueriesAfter = $responseAfter->viewData('topQueries');
            $this->assertEquals($uniqueKey . '_alpha', $topQueriesAfter->first()->query);
            $this->assertEquals(2, $topQueriesAfter->first()->count);
        } finally {
            AnalyticsSearch::where('query', 'LIKE', "{$uniqueKey}%")->delete();
        }
    }
}
