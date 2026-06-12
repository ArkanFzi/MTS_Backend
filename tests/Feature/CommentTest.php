<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_comment_on_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'body' => 'This is a comment',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', ['body' => 'This is a comment']);
        
        $user->refresh();
        $this->assertEquals(5, $user->reputation_points);
    }

    public function test_non_moderator_cannot_delete_comment()
    {
        $user = $this->actingAsUser();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/moderator/posts/{$comment->post_id}/comments/{$comment->id}");

        $response->assertStatus(403);
    }

    public function test_moderator_can_delete_any_comment()
    {
        $this->actingAsUser(null, 'moderator');
        $comment = Comment::factory()->create();

        $response = $this->deleteJson("/api/moderator/posts/{$comment->post_id}/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    public function test_only_post_owner_can_accept_answer()
    {
        $postOwner = $this->createUser();
        $post = Post::factory()->create(['user_id' => $postOwner->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->actingAsUser(); // Different user

        $response = $this->postJson("/api/posts/{$post->id}/comments/{$comment->id}/accept");
        $response->assertStatus(403);

        // Now as owner
        $this->actingAsUser($postOwner);
        $response = $this->postJson("/api/posts/{$post->id}/comments/{$comment->id}/accept");
        $response->assertStatus(200);

        $comment->refresh();
        $this->assertTrue($comment->is_accepted);

        // Point check for comment owner (+15)
        $commentOwner = $comment->user;
        $this->assertEquals(15, $commentOwner->reputation_points);
    }

    public function test_can_get_post_comments()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();
        Comment::factory()->count(2)->create(['post_id' => $post->id]);

        $response = $this->getJson("/api/posts/{$post->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_owner_can_update_comment()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id, 'body' => 'Old comment body']);

        $response = $this->putJson("/api/posts/{$post->id}/comments/{$comment->id}", [
            'body' => 'Updated comment body',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated comment body', $comment->fresh()->body);
    }

    public function test_user_can_reply_to_comment()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->postJson("/api/posts/{$post->id}/comments/{$comment->id}/replies", [
            'body' => 'This is a reply body',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Balasan berhasil ditambahkan');

        $this->assertDatabaseHas('comments', [
            'parent_id' => $comment->id,
            'body' => 'This is a reply body',
        ]);
    }

    public function test_owner_can_update_reply()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'parent_id' => $comment->id,
            'body' => 'Old reply body',
        ]);

        $response = $this->putJson("/api/posts/{$post->id}/comments/{$comment->id}/replies/{$reply->id}", [
            'body' => 'Updated reply body',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Balasan berhasil diedit');

        $this->assertEquals('Updated reply body', $reply->fresh()->body);
    }
}
