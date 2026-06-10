<?php

namespace Tests\Feature\Modules\Admin;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F10_CategoryMasterTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        // Asumsi: sistem role menggunakan metode hasRole
        $this->admin->roles()->create(['name' => 'admin']); 
    }

    /** @test */
    public function admin_can_create_category()
    {
        $payload = ['name' => 'Technology', 'slug' => 'technology'];
        
        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/moderator/categories', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Technology']);
    }
}
