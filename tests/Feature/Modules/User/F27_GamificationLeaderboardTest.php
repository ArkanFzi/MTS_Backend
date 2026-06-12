<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F27_GamificationLeaderboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_leaderboard_by_period()
    {
        $response = $this->getJson('/api/explore/leaderboard?period=week');
        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }
}
