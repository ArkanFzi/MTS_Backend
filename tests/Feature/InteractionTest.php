<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_cannot_like_own_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/likes/toggle', [
            'target_type' => 'post',
            'target_id' => $post->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_like_other_post()
    {
        $user = $this->actingAsUser();
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Like
        $this->postJson('/api/likes/toggle', [
            'target_type' => 'post',
            'target_id' => $post->id,
        ]);

        $owner->refresh();
        $this->assertEquals(2, $owner->reputation_points);

        // Unlike
        $this->postJson('/api/likes/toggle', [
            'target_type' => 'post',
            'target_id' => $post->id,
        ]);

        $owner->refresh();
        $this->assertEquals(0, $owner->reputation_points);
    }

    public function test_user_cannot_vote_own_post()
    {
        $user = $this->actingAsUser();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/votes', [
            'target_type' => 'post',
            'target_id' => $post->id,
            'type' => 'up',
        ]);

        $response->assertStatus(403);
    }

    public function test_post_upvote_gives_5_points()
    {
        $user = $this->actingAsUser();
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->postJson('/api/votes', [
            'target_type' => 'post',
            'target_id' => $post->id,
            'type' => 'up',
        ]);

        $owner->refresh();
        $this->assertEquals(5, $owner->reputation_points);
    }

    public function test_comment_upvote_gives_3_points()
    {
        $user = $this->actingAsUser();
        $owner = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);

        $this->postJson('/api/votes', [
            'target_type' => 'comment',
            'target_id' => $comment->id,
            'type' => 'up',
        ]);

        $owner->refresh();
        $this->assertEquals(3, $owner->reputation_points);
    }

    public function test_downvote_removes_2_points()
    {
        $user = $this->actingAsUser();
        $owner = User::factory()->create(['reputation_points' => 10]);
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->postJson('/api/votes', [
            'target_type' => 'post',
            'target_id' => $post->id,
            'type' => 'down',
        ]);

        $owner->refresh();
        $this->assertEquals(8, $owner->reputation_points);
    }

    public function test_cannot_follow_yourself()
    {
        $user = $this->actingAsUser();

        $response = $this->postJson("/api/users/{$user->id}/follow");

        $response->assertStatus(422);
    }

    public function test_user_can_follow_others()
    {
        $user = $this->actingAsUser();
        $other = User::factory()->create();

        $response = $this->postJson("/api/users/{$other->id}/follow");

        $response->assertStatus(200);
        $this->assertDatabaseHas('follows', [
            'follower_id' => $user->id,
            'following_id' => $other->id,
        ]);
    }

    public function test_user_can_get_followers()
    {
        $user = $this->actingAsUser();
        $other = User::factory()->create();
        
        // Simulasikan $other mem-follow $user
        \App\Models\Interaction\Follow::create([
            'follower_id' => $other->id,
            'following_id' => $user->id,
        ]);

        $response = $this->getJson("/api/users/{$user->id}/followers");

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_user_can_get_following()
    {
        $user = $this->actingAsUser();
        $other = User::factory()->create();
        
        // Simulasikan $user mem-follow $other
        \App\Models\Interaction\Follow::create([
            'follower_id' => $user->id,
            'following_id' => $other->id,
        ]);

        $response = $this->getJson("/api/users/{$user->id}/following");

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }
}
