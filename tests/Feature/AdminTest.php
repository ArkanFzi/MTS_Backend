<?php

namespace Tests\Feature;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_view_stats_overview()
    {
        $this->actingAsUser(null, 'admin');

        $response = $this->getJson('/api/admin/stats/overview');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'summary' => ['total_users', 'total_posts']
                ]
            ]);
    }

    public function test_admin_can_change_user_role()
    {
        $this->actingAsUser(null, 'admin');
        $targetUser = User::factory()->create();

        $response = $this->putJson("/api/admin/users/{$targetUser->id}/role", [
            'role' => 'moderator',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($targetUser->fresh()->hasRole('moderator'));
    }

    public function test_admin_can_reset_user_password()
    {
        $this->actingAsUser(null, 'admin');
        $targetUser = User::factory()->create();

        $response = $this->putJson("/api/admin/users/{$targetUser->id}/reset-password", [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
    }

    public function test_moderator_cannot_access_admin_routes()
    {
        $this->actingAsUser(null, 'moderator');

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_points_summary()
    {
        $this->actingAsUser(null, 'admin');

        $response = $this->getJson('/api/admin/stats/points-summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_admin_can_view_roles()
    {
        $this->actingAsUser(null, 'admin');

        $response = $this->getJson('/api/admin/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_admin_can_list_users()
    {
        $this->actingAsUser(null, 'admin');
        User::factory()->count(2)->create();

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                ]
            ]);
    }

    public function test_admin_can_view_user_details()
    {
        $this->actingAsUser(null, 'admin');
        $targetUser = User::factory()->create(['username' => 'targetusername']);

        $response = $this->getJson("/api/admin/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.username', 'targetusername');
    }

    public function test_admin_can_update_user_profile()
    {
        $this->actingAsUser(null, 'admin');
        $targetUser = User::factory()->create(['username' => 'oldusername']);

        $response = $this->putJson("/api/admin/users/{$targetUser->id}/profile", [
            'username' => 'newusername',
            'bio' => 'updated bio',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $targetUser->refresh();
        $this->assertEquals('newusername', $targetUser->username);
        $this->assertEquals('updated bio', $targetUser->bio);
    }
}
