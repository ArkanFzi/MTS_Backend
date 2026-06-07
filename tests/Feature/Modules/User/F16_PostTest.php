<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F16_PostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_view_all_posts()
    {
        Post::factory()->count(3)->create(['status' => 'open']);
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_create_a_post()
    {
        $category = \App\Models\Content\Category::factory()->create();
        $payload = [
            'title' => 'Test Post', 
            'body' => 'Test Body',
            'category_id' => $category->id
        ];
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/posts', $payload);
        $response->assertStatus(201);
    }
}
