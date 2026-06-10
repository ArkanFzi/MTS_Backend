<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F21_CommentEditHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_comment_edit_creates_history_record()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id, 'created_at' => now()->subMinutes(1)]);
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/posts/{$comment->post_id}/comments/{$comment->id}", [
                             'body' => 'Updated Comment Body'
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comment_edit_history', ['comment_id' => $comment->id]);
    }
}
