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
}
