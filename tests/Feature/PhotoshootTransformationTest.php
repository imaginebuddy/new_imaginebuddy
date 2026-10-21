<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Photoshoot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PhotoshootTransformationTest extends TestCase
{
    protected function getAdminUser()
    {
        return User::find(1) ?? User::where('role', '1')->first();
    }

    /**
     * Test admin add photoshoot form displays visible raw image upload input.
     */
    public function test_admin_add_form_displays_raw_image_input(): void
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get('panel/admin/photoshoots/add');
        $response->assertStatus(200);
        $response->assertSee('name="raw_image"', false);
        $response->assertSee('custom-file');
    }

    /**
     * Test admin edit photoshoot form displays visible raw image upload input.
     */
    public function test_admin_edit_form_displays_raw_image_input(): void
    {
        $admin = $this->getAdminUser();
        $photoshoot = Photoshoot::create([
            'uuid' => 'test_' . uniqid(),
            'title' => 'Form Visibility Test ' . uniqid(),
            'slug' => 'form-visibility-test-' . uniqid(),
            'user_id' => $admin ? $admin->id : 1,
        ]);

        $response = $this->actingAs($admin)->get('panel/admin/photoshoots/edit/' . $photoshoot->id);
        $response->assertStatus(200);
        $response->assertSee('name="raw_image"', false);
        $response->assertSee('custom-file');

        $photoshoot->delete();
    }

    /**
     * Test photoshoot model raw_image field and raw_image_url accessor.
     */
    public function test_photoshoot_model_raw_image_attribute_and_url(): void
    {
        $admin = $this->getAdminUser();

        $photoshoot = Photoshoot::create([
            'uuid' => 'test_' . uniqid(),
            'title' => 'Test Transformation Unit Photoshoot',
            'slug' => 'test-transformation-unit-' . uniqid(),
            'user_id' => $admin ? $admin->id : 1,
            'raw_image' => 'sample-raw-test.jpg',
        ]);

        $this->assertEquals('sample-raw-test.jpg', $photoshoot->raw_image);
        $this->assertNotNull($photoshoot->raw_image_url);
        $this->assertStringContainsString('sample-raw-test.jpg', $photoshoot->raw_image_url);

        $photoshoot->delete();
    }

    /**
     * Test admin can create and update photoshoot with high-res transformed image.
     */
    public function test_admin_can_create_and_update_transformed_image(): void
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            $this->markTestSkipped('No admin user found to test admin creation.');
        }

        $rawImage = UploadedFile::fake()->image('raw_sample.jpg', 1200, 1200);
        $transImage = UploadedFile::fake()->image('trans_sample.jpg', 1200, 1200);
        $title = 'Test Admin Transformed ' . uniqid();

        $response = $this->actingAs($admin)->post('panel/admin/photoshoots/add', [
            'title' => $title,
            'raw_image' => $rawImage,
            'transformed_image' => $transImage,
        ]);

        $photoshoot = Photoshoot::where('title', $title)->first();
        $this->assertNotNull($photoshoot);
        $this->assertNotEmpty($photoshoot->raw_image);
        $this->assertNotEmpty($photoshoot->transformed_image);

        $path = config('path.photoshoots_transformed', 'uploads/photoshoots/transformed/');
        $fullPath = public_path($path . $photoshoot->transformed_image);
        $this->assertTrue(File::exists($fullPath));

        // Test display on public detail page
        $detailResponse = $this->get('photoshoots/' . $photoshoot->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($photoshoot->transformed_image);

        // Test removal
        $this->actingAs($admin)->post('panel/admin/photoshoots/update', [
            'id' => $photoshoot->id,
            'title' => $photoshoot->title,
            'remove_transformed_image' => '1',
        ]);

        $photoshoot->refresh();
        $this->assertNull($photoshoot->transformed_image);
        $this->assertFalse(File::exists($fullPath));

        $photoshoot->delete();
    }

    /**
     * Test admin can create photoshoot with an uploaded raw product image.
     */
    public function test_admin_can_create_photoshoot_with_raw_image(): void
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            $this->markTestSkipped('No admin user found to test admin creation.');
        }

        $image = UploadedFile::fake()->image('raw_sample_camera.jpg', 800, 600);
        $title = 'Test Admin Created Photoshoot ' . uniqid();

        $response = $this->actingAs($admin)->post('panel/admin/photoshoots/add', [
            'title' => $title,
            'slug' => '',
            'description' => 'Test photoshoot with raw image upload.',
            'raw_image' => $image,
        ]);

        $photoshoot = Photoshoot::where('title', $title)->first();
        $this->assertNotNull($photoshoot);
        $this->assertNotEmpty($photoshoot->raw_image);

        // Verify the file exists on disk
        $path = config('path.photoshoots_raw', 'uploads/photoshoots/raw/');
        $fullPath = public_path($path . $photoshoot->raw_image);
        $this->assertTrue(File::exists($fullPath));

        // Cleanup
        $photoshoot->delete();
        $this->assertFalse(File::exists($fullPath));
    }

    /**
     * Test admin can update and replace raw image, and then remove it.
     */
    public function test_admin_can_update_replace_and_remove_raw_image(): void
    {
        $admin = $this->getAdminUser();
        if (!$admin) {
            $this->markTestSkipped('No admin user found to test admin update.');
        }

        $initialImage = UploadedFile::fake()->image('raw_initial.jpg', 600, 600);

        // Create initial photoshoot
        $title = 'Test Admin Update Photoshoot ' . uniqid();
        $this->actingAs($admin)->post('panel/admin/photoshoots/add', [
            'title' => $title,
            'raw_image' => $initialImage,
        ]);

        $photoshoot = Photoshoot::where('title', $title)->first();
        $this->assertNotNull($photoshoot);
        $oldImageName = $photoshoot->raw_image;
        $oldFullPath = public_path(config('path.photoshoots_raw', 'uploads/photoshoots/raw/') . $oldImageName);
        $this->assertTrue(File::exists($oldFullPath));

        // Replace raw image
        $replacementImage = UploadedFile::fake()->image('raw_replacement.jpg', 700, 700);
        $updateResponse = $this->actingAs($admin)->post('panel/admin/photoshoots/update', [
            'id' => $photoshoot->id,
            'title' => $title . ' Updated',
            'raw_image' => $replacementImage,
        ]);

        $photoshoot->refresh();
        $newImageName = $photoshoot->raw_image;
        $this->assertNotEquals($oldImageName, $newImageName);
        $this->assertFalse(File::exists($oldFullPath));
        $newFullPath = public_path(config('path.photoshoots_raw', 'uploads/photoshoots/raw/') . $newImageName);
        $this->assertTrue(File::exists($newFullPath));

        // Remove raw image
        $removeResponse = $this->actingAs($admin)->post('panel/admin/photoshoots/update', [
            'id' => $photoshoot->id,
            'title' => $photoshoot->title,
            'remove_raw_image' => '1',
        ]);

        $photoshoot->refresh();
        $this->assertNull($photoshoot->raw_image);
        $this->assertFalse(File::exists($newFullPath));

        $photoshoot->delete();
    }

    /**
     * Test public photoshoot detail page displays before-and-after transformation.
     */
    public function test_public_photoshoot_detail_page_displays_transformation(): void
    {
        $admin = $this->getAdminUser();

        $image = UploadedFile::fake()->image('public_test_raw.jpg', 800, 800);
        $title = 'Test Public Transformation ' . uniqid();

        $this->actingAs($admin)->post('panel/admin/photoshoots/add', [
            'title' => $title,
            'raw_image' => $image,
        ]);

        $photoshoot = Photoshoot::where('title', $title)->first();
        $this->assertNotNull($photoshoot);

        $response = $this->get('photoshoots/' . $photoshoot->slug);
        $response->assertStatus(200);
        $response->assertSee('Before &amp; After Included', false);
        $response->assertSee('Before: Raw Product');
        $response->assertSee('After: AI Photoshoot');
        $response->assertSee($photoshoot->raw_image);

        $photoshoot->delete();
    }

    /**
     * Test public photoshoot detail page without raw image loads gracefully.
     */
    public function test_public_photoshoot_detail_page_without_raw_image(): void
    {
        $admin = $this->getAdminUser();

        $title = 'Test Without Raw ' . uniqid();
        $photoshoot = Photoshoot::create([
            'uuid' => 'test_' . uniqid(),
            'title' => $title,
            'slug' => 'test-without-raw-' . uniqid(),
            'user_id' => $admin ? $admin->id : 1,
            'raw_image' => null,
        ]);

        $response = $this->get('photoshoots/' . $photoshoot->slug);
        $response->assertStatus(200);
        $response->assertDontSee('Before &amp; After Included', false);
        // Page should still contain the transformation showcase container without breaking
        $response->assertSee('transformation-section');

        $photoshoot->delete();
    }
}
