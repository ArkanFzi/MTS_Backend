<?php

namespace Tests\Feature\Modules\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\Content\Post;
use App\Models\Content\Comment;

class F20_NestedCommentReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_nested_comment_reply()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson("/api/posts/{$post->id}/comments/{$comment->id}/replies", ['body' => 'Reply']);
        $response->assertStatus(201);
    }
}
