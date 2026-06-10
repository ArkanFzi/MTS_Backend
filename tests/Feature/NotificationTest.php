<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_receives_notification_when_post_is_liked()
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        
        $liker = $this->actingAsUser();

        $this->postJson('/api/likes/toggle', [
            'target_type' => 'post',
            'target_id' => $post->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'like_post',
        ]);
    }

    public function test_user_receives_notification_when_followed()
    {
        $other = User::factory()->create();
        $user = $this->actingAsUser();

        $this->postJson("/api/users/{$other->id}/follow");

        $this->assertDatabaseHas('notifications', [
            'user_id' => $other->id,
            'type' => 'new_follower',
        ]);
    }

    public function test_user_can_mark_notifications_as_read()
    {
        $user = $this->actingAsUser();
        $notification = \App\Models\Moderation\Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'data' => json_encode(['message' => 'test']),
            'is_read' => false,
            'created_at' => now(),
        ]);

        $response = $this->patchJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertTrue($notification->fresh()->is_read);
    }
}
