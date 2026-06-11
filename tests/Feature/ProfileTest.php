<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

   public function test_user_can_get_profile()
{
    $this->actingAsUser();

    $response = $this->getJson('/api/settings/profile');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
}
    public function test_user_can_update_profile_text()
    {
        $this->actingAsUser();

        $response = $this->putJson('/api/settings/profile', [
            'bio' => 'Bio baru saya',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.bio', 'Bio baru saya');
    }

    public function test_user_can_update_profile_with_avatar()
    {
        Storage::fake('public');
        $this->actingAsUser();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/settings/profile', [
            '_method' => 'PUT',
            'bio'     => 'Bio dengan foto',
            'avatar'  => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.bio', 'Bio dengan foto');

        $this->assertNotNull($response->json('data.avatar_url'));
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

    public function test_user_can_update_password()
    {
        $user = User::factory()->create([
            'password_hash' => bcrypt('oldpassword123'),
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/settings/password', [
            'old_password'          => 'oldpassword123',
            'new_password'          => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_user_cannot_update_password_with_wrong_old_password()
    {
        $user = User::factory()->create([
            'password_hash' => bcrypt('oldpassword123'),
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/settings/password', [
            'old_password'              => 'wrongpassword',
            'new_password'              => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_guest_cannot_access_profile()
    {
        $response = $this->getJson('/api/settings/profile');

        $response->assertStatus(401);
    }
}