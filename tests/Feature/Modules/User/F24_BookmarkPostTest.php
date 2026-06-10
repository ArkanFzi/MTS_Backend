<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F24_BookmarkPostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_bookmark_post()
    {
        $post = Post::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/bookmarks/toggle', [
                             'post_id' => $post->id
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookmarks', ['user_id' => $this->user->id, 'post_id' => $post->id]);
    }
}
