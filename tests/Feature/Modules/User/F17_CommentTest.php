<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F17_CommentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_comment_on_post()
    {
        $post = Post::factory()->create();
        $payload = ['body' => 'Test Comment'];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/posts/{$post->id}/comments", $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', ['body' => 'Test Comment', 'post_id' => $post->id]);
    }
}
