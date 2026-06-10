<?php

namespace Tests\Feature\Modules\Admin;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F11_BadgeMasterTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->roles()->create(['name' => 'admin']);
    }

    /** @test */
    public function admin_can_create_badge()
    {
        $payload = [
            'name' => 'Top Contributor', 
            'description' => 'Test desc', 
            'icon_url' => 'https://example.com/test.png', 
            'tier' => 'gold', 
            'condition_type' => 'post_count', 
            'condition_value' => 10
        ];
        
        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/moderator/badges', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('badges', ['name' => 'Top Contributor']);
    }
}
