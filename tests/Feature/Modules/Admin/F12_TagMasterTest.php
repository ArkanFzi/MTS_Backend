<?php

namespace Tests\Feature\Modules\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F12_TagMasterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_tag_master()
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/moderator/tags');
        $response->assertStatus(200);
    }
}
