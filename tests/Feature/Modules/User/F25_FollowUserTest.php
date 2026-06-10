<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F25_FollowUserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_follow_another_user()
    {
        $targetUser = User::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/users/{$targetUser->id}/follow");

        $response->assertStatus(200);
        $this->assertDatabaseHas('follows', ['follower_id' => $this->user->id, 'following_id' => $targetUser->id]);
    }
}
