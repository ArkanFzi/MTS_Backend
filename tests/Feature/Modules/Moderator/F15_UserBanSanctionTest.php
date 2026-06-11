<?php

namespace Tests\Feature\Modules\Moderator;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F15_UserBanSanctionTest extends TestCase
{
    use RefreshDatabase;

    protected $moderator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moderator = User::factory()->create();
        $this->moderator->roles()->create(['name' => 'moderator']);
    }

    /** @test */
    public function moderator_can_ban_user()
    {
        $targetUser = User::factory()->create();
        
        $response = $this->actingAs($this->moderator, 'sanctum')
                         ->postJson("/api/moderator/bans/{$targetUser->id}/ban", [
                             'reason' => 'Spamming'
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $targetUser->id, 'is_banned' => true]);
        $this->assertDatabaseHas('moderation_logs', [
            'target_user_id' => $targetUser->id,
            'action_type' => 'ban_user',
            'reason' => 'Spamming'
        ]);
    }
}
