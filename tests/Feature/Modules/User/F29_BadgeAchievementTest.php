<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Gamification\Badge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F29_BadgeAchievementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_badge_achievement()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create(['name' => 'First Post']);
        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        $this->assertDatabaseHas('user_badges', ['user_id' => $user->id, 'badge_id' => $badge->id]);
    }
}
