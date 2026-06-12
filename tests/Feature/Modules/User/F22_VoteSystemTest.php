<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F22_VoteSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_vote_on_post()
    {
        $post = Post::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/votes', [
                             'target_id' => $post->id,
                             'target_type' => 'post',
                             'type' => 'up',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('votes', ['user_id' => $this->user->id, 'target_id' => $post->id]);
    }
}
