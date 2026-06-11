<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F23_LikeSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_like_post()
    {
        $post = Post::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/likes/toggle', [
                             'target_id' => $post->id,
                             'target_type' => 'post'
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', ['user_id' => $this->user->id, 'target_id' => $post->id, 'target_type' => 'post']);
    }

    /** @test */
    public function user_can_like_comment()
    {
        $comment = \App\Models\Content\Comment::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/likes/toggle', [
                             'target_id' => $comment->id,
                             'target_type' => 'comment'
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', ['user_id' => $this->user->id, 'target_id' => $comment->id, 'target_type' => 'comment']);
    }
}
