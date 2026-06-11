<?php

namespace Tests\Feature;

use App\Models\Content\Post;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_bookmark_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();

        $response = $this->postJson('/api/bookmarks/toggle', [
            'post_id' => $post->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'bookmarked');
    }

    public function test_user_can_unbookmark_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();

        // Bookmark dulu
        $this->postJson('/api/bookmarks/toggle', ['post_id' => $post->id]);

        // Unbookmark
        $response = $this->postJson('/api/bookmarks/toggle', [
            'post_id' => $post->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'unbookmarked');
    }

    public function test_user_can_get_bookmarks()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();

        $this->postJson('/api/bookmarks/toggle', ['post_id' => $post->id]);

        $response = $this->getJson('/api/bookmarks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    public function test_guest_cannot_bookmark()
    {
        $post = Post::factory()->create();

        $response = $this->postJson('/api/bookmarks/toggle', [
            'post_id' => $post->id,
        ]);

        $response->assertStatus(401);
    }
}