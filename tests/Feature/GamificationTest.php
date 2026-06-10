<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Gamification\Badge;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_levels_up_every_50_points()
    {
        $user = User::factory()->create(['reputation_points' => 45, 'level' => 1]);
        
        // Use the service to add points as it contains the level-up logic
        $service = app(BadgeAchievementService::class);
        $service->addPoints($user, 10, 'test_action');

        $this->assertEquals(2, $user->fresh()->level);
    }

    public function test_badge_is_awarded_automatically_on_reputation_milestone()
    {
        $badge = Badge::factory()->create([
            'name' => 'Elite Contributor',
            'condition_type' => 'reputation_points',
            'condition_value' => 100,
        ]);

        $user = User::factory()->create(['reputation_points' => 95]);

        // Cross the threshold via service
        $service = app(BadgeAchievementService::class);
        $service->addPoints($user, 10, 'test_action');

        $this->assertTrue($user->badges()->where('badge_id', $badge->id)->exists());
    }

    public function test_badge_is_awarded_on_post_count_milestone()
    {
        $badge = Badge::factory()->create([
            'name' => 'Frequent Poster',
            'condition_type' => 'post_count',
            'condition_value' => 5,
        ]);

        $user = $this->actingAsUser(['reputation_points' => 100]); // Ensure they can post
        $category = \App\Models\Content\Category::factory()->create();

        // Create 5 posts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/posts', [
                'title' => "Post $i",
                'body' => 'Content',
                'category_id' => $category->id,
            ]);
        }

        $this->assertTrue($user->fresh()->badges()->where('badge_id', $badge->id)->exists());
    }
}
