<?php

namespace Tests\Feature\Modules\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F9_UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_user_management()
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/users');
        $response->assertStatus(200);
    }
}
