<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Content\Category;
use App\Models\Content\Post;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_public_can_list_posts()
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_needs_15_points_to_create_post()
    {
        $user = $this->actingAsUser(['reputation_points' => 10]);
        $category = Category::factory()->create();

        $response = $this->postJson('/api/posts', [
            'title' => 'My New Post',
            'body' => 'Content of the post',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_with_sufficient_points_can_create_post()
    {
        $user = $this->actingAsUser(['reputation_points' => 20]);
        $category = Category::factory()->create();

        $response = $this->postJson('/api/posts', [
            'title' => 'Valid Post',
            'body' => 'Content of the post',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', ['title' => 'Valid Post']);
        
        // Test points gain after post (+10)
        $user->refresh();
        $this->assertEquals(30, $user->reputation_points);
    }

    public function test_owner_can_update_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated content',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
    }

    public function test_non_owner_cannot_update_post()
    {
        $owner = $this->createUser();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        
        $this->actingAsUser(); // Logged in as different user

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
