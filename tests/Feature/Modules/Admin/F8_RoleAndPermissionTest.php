<?php

namespace Tests\Feature\Modules\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F8_RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_role_and_permission()
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/roles');
        $response->assertStatus(200);
    }
}
