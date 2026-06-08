<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Content\Category;
use App\Models\Moderation\Report;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_moderator_can_create_category()
    {
        $this->actingAsUser(null, 'moderator');

        $response = $this->postJson('/api/moderator/categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
            'description' => 'Description here',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function test_regular_user_cannot_access_moderator_routes()
    {
        $this->actingAsUser(null, 'user');

        $response = $this->getJson('/api/moderator/reports');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_moderator_routes()
    {
        $this->actingAsUser(null, 'admin');

        $response = $this->getJson('/api/moderator/reports');

        $response->assertStatus(200);
    }

    public function test_moderator_can_warn_user()
    {
        $this->actingAsUser(null, 'moderator');
        $targetUser = User::factory()->create(['reputation_points' => 10]);

        $response = $this->postJson("/api/moderator/bans/{$targetUser->id}/warn", [
            'reason' => 'Spamming',
        ]);

        $response->assertStatus(200);
        $targetUser->refresh();
        // Warning logic in project: Warning gives -5 points
        $this->assertEquals(5, $targetUser->reputation_points);
    }

    public function test_moderator_can_ban_user()
    {
        $this->actingAsUser(null, 'moderator');
        $targetUser = User::factory()->create(['reputation_points' => 30]);

        $response = $this->postJson("/api/moderator/bans/{$targetUser->id}/ban", [
            'reason' => 'Severe misconduct',
        ]);

        $response->assertStatus(200);
        $targetUser->refresh();
        $this->assertTrue($targetUser->is_banned);
        // Ban logic in project: Ban gives -20 points
        $this->assertEquals(10, $targetUser->reputation_points);
    }

    public function test_accepting_report_deducts_10_points()
    {
        $this->actingAsUser(null, 'moderator');
        $targetUser = User::factory()->create(['reputation_points' => 20]);
        
        $post = \App\Models\Content\Post::factory()->create(['user_id' => $targetUser->id]);

        $report = Report::create([
            'reporter_id' => $this->createUser()->id,
            'target_id' => $post->id,
            'target_type' => 'post',
            'reason' => 'Spam',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->putJson("/api/moderator/reports/{$report->id}", [
            'status' => 'resolved',
            'action' => 'none',
        ]);

        $response->assertStatus(200);
        $targetUser->refresh();
        $this->assertEquals(10, $targetUser->reputation_points);
    }
}
