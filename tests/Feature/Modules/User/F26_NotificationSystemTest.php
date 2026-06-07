<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F26_NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_user_receives_notification_on_new_comment()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $commenter = User::factory()->create();

        $this->actingAs($commenter, 'sanctum')
             ->postJson("/api/posts/{$post->id}/comments", ['body' => 'Hello']);

        $this->assertDatabaseHas('notifications', ['user_id' => $this->user->id]);
    }
}
