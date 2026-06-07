<?php

namespace Tests\Feature\Modules\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\Content\Post;

class F18_MarkAcceptedAnswerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_mark_accepted_answer()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = \App\Models\Content\Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/posts/{$post->id}/comments/{$comment->id}/accept");
        $response->assertStatus(200);
    }
}
