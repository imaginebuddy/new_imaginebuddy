<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AdminSettings;

class MetaAdsSettingsTest extends TestCase
{
    protected ?User $admin = null;
    protected ?string $originalStatus = null;
    protected ?string $originalCode = null;
    protected ?string $originalId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', '1')->first() ?? User::whereNotNull('role')->first();

        $settings = AdminSettings::first();
        if ($settings) {
            $this->originalStatus = $settings->meta_pixel_status;
            $this->originalCode   = $settings->meta_pixel;
            $this->originalId     = $settings->meta_pixel_id;
        }
    }

    protected function tearDown(): void
    {
        if ($this->originalStatus !== null) {
            $settings = AdminSettings::first();
            if ($settings) {
                $settings->meta_pixel_status = $this->originalStatus;
                $settings->meta_pixel        = $this->originalCode;
                $settings->meta_pixel_id     = $this->originalId;
                $settings->save();
            }
        }

        parent::tearDown();
    }

    /**
     * Test guest cannot access meta ads settings page.
     */
    public function test_guest_cannot_access_meta_ads_settings(): void
    {
        $response = $this->get('panel/admin/meta-ads');
        $response->assertRedirect('login');
    }

    /**
     * Test admin can view meta ads settings page.
     */
    public function test_admin_can_view_meta_ads_page(): void
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $response = $this->actingAs($this->admin)->get('panel/admin/meta-ads');

        $response->assertStatus(200);
        $response->assertSee('Meta Ads');
        $response->assertSee('meta_pixel_status');
        $response->assertSee('meta_pixel');
        $response->assertSee('Paste your Meta Pixel code here...');
    }

    /**
     * Test admin can update meta pixel settings by pasting complete code and enabling tracking.
     */
    public function test_admin_can_save_complete_meta_pixel_code_and_enable(): void
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $testPixelId = '987654321098765';
        $fullCode = <<<HTML
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$testPixelId}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$testPixelId}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
HTML;

        $response = $this->actingAs($this->admin)->post('panel/admin/meta-ads', [
            'meta_pixel_status' => '1',
            'meta_pixel' => $fullCode,
        ]);

        $response->assertRedirect('panel/admin/meta-ads');
        $response->assertSessionHas('success_message');

        $settings = AdminSettings::first();
        $this->assertEquals(1, $settings->meta_pixel_status);
        $this->assertEquals($fullCode, $settings->meta_pixel);
        $this->assertEquals($testPixelId, $settings->meta_pixel_id);
    }

    /**
     * Test frontend injects meta pixel when enabled.
     */
    public function test_frontend_renders_pixel_when_enabled(): void
    {
        $testPixelId = '987654321098765';
        $fullCode = "<!-- Meta Pixel Code --><script>fbq('init', '{$testPixelId}'); fbq('track', 'PageView');</script><noscript><img src=\"https://www.facebook.com/tr?id={$testPixelId}&ev=PageView&noscript=1\" /></noscript><!-- End Meta Pixel Code -->";

        $settings = AdminSettings::first();
        $settings->meta_pixel_status = 1;
        $settings->meta_pixel = $fullCode;
        $settings->meta_pixel_id = $testPixelId;
        $settings->save();
        view()->share('settings', $settings);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee("fbq('init', '{$testPixelId}')", false);
        $response->assertSee("fbq('track', 'PageView')", false);
        $response->assertSee("https://www.facebook.com/tr?id={$testPixelId}&ev=PageView&noscript=1", false);
    }

    /**
     * Test frontend does NOT render meta pixel when disabled.
     */
    public function test_frontend_does_not_render_pixel_when_disabled(): void
    {
        $testPixelId = '987654321098765';
        $fullCode = "<!-- Meta Pixel Code --><script>fbq('init', '{$testPixelId}'); fbq('track', 'PageView');</script><noscript><img src=\"https://www.facebook.com/tr?id={$testPixelId}&ev=PageView&noscript=1\" /></noscript><!-- End Meta Pixel Code -->";

        $settings = AdminSettings::first();
        $settings->meta_pixel_status = 0;
        $settings->meta_pixel = $fullCode;
        $settings->meta_pixel_id = $testPixelId;
        $settings->save();
        view()->share('settings', $settings);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee("fbq('init', '{$testPixelId}')", false);
        $response->assertDontSee("https://www.facebook.com/tr?id={$testPixelId}&ev=PageView&noscript=1", false);
    }
}
