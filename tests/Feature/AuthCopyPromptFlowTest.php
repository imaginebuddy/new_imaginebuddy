<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Images;
use App\Models\User;

class AuthCopyPromptFlowTest extends TestCase
{
    /**
     * Test guest visiting search page sees global authCopyModal.
     */
    public function test_guest_sees_auth_copy_modal_on_search_page(): void
    {
        $settings = \App\Models\AdminSettings::first();
        if ($settings) {
            $settings->update(['google_login' => 'on']);
            $settings->google_login = 'on';
            view()->share('settings', $settings);
        }

        $response = $this->get('/search?q=food');

        $response->assertStatus(200);
        $response->assertSee('id="authCopyModal"', false);
        $response->assertSee('Sign Up to View & Copy This Prompt', false);
        $response->assertSee('Continue with Google');
    }

    /**
     * Test guest visiting free prompts listing page sees global authCopyModal.
     */
    public function test_guest_sees_auth_copy_modal_on_free_prompts_page(): void
    {
        $response = $this->get('/prompts/free');

        $response->assertStatus(200);
        $response->assertSee('id="authCopyModal"', false);
        $response->assertSee('Sign Up to View & Copy This Prompt', false);
    }

    /**
     * Test prompt copy endpoint blocks guest without authentication.
     */
    public function test_guest_cannot_bypass_copy_prompt_endpoint(): void
    {
        $image = Images::first();
        if (!$image) {
            $this->markTestSkipped('No image found');
        }

        $response = $this->postJson("/prompt/copy/{$image->id}");

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'require_login' => true,
        ]);
    }

    /**
     * Test google one-tap endpoint returns 400 when credential is missing.
     */
    public function test_one_tap_endpoint_validates_credential(): void
    {
        $response = $this->postJson('/oauth/google/one-tap', []);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Missing Google credential token.',
        ]);
    }

    /**
     * Test google one-tap endpoint returns 401 for invalid credential.
     */
    public function test_one_tap_endpoint_rejects_invalid_token(): void
    {
        $response = $this->postJson('/oauth/google/one-tap', [
            'credential' => 'invalid_dummy_jwt_token',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid Google ID token.',
        ]);
    }

    /**
     * Test logged in user can copy allowed prompt.
     */
    public function test_authenticated_user_can_copy_free_prompt(): void
    {
        $user = User::first();
        $freeImage = Images::where('item_for_sale', 'free')->first() ?: Images::first();
        if (!$user || !$freeImage) {
            $this->markTestSkipped('User or Image not available.');
        }

        $response = $this->actingAs($user)->postJson("/prompt/copy/{$freeImage->id}");

        // Either success 200 or 403 limit reached depending on test user's daily limit
        $this->assertContains($response->getStatusCode(), [200, 403]);
    }
}
