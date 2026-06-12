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

    public function test_moderator_can_update_category()
    {
        $this->actingAsUser(null, 'moderator');
        $category = Category::factory()->create(['name' => 'Old CatName']);

        $response = $this->putJson("/api/moderator/categories/{$category->id}", [
            'name' => 'Updated CatName',
            'slug' => 'updated-catname',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated CatName', $category->fresh()->name);
    }

    public function test_moderator_can_delete_category()
    {
        $this->actingAsUser(null, 'moderator');
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/moderator/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_moderator_can_crud_tags()
    {
        $this->actingAsUser(null, 'moderator');

        // 1. Create Tag
        $response = $this->postJson('/api/moderator/tags', [
            'name' => 'TagBaru',
            'color' => '#ffffff',
        ]);
        $response->assertStatus(201);
        $tagId = $response->json('data.id');

        // 2. Read Tags List
        $response = $this->getJson('/api/explore/tags'); // Using explore route as it's public/mapped
        $response->assertStatus(200);

        // 3. Update Tag
        $response = $this->putJson("/api/moderator/tags/{$tagId}", [
            'name' => 'TagUpdated',
            'color' => '#000000',
        ]);
        $response->assertStatus(200);

        // 4. Delete Tag
        $response = $this->deleteJson("/api/moderator/tags/{$tagId}");
        $response->assertStatus(200);
    }

    public function test_moderator_can_crud_badges()
    {
        $this->actingAsUser(null, 'moderator');

        // 1. Create Badge
        $response = $this->postJson('/api/moderator/badges', [
            'name' => 'Badge Baru',
            'description' => 'Untuk test saja',
            'icon_url' => 'https://example.com/icon.png',
            'tier' => 'bronze',
            'condition_type' => 'reputation_points',
            'condition_value' => 50,
        ]);
        $response->assertStatus(201);
        $badgeId = $response->json('data.id');

        // 2. Read Badge List
        $response = $this->getJson('/api/moderator/badges');
        $response->assertStatus(200);

        // 3. Update Badge
        $response = $this->putJson("/api/moderator/badges/{$badgeId}", [
            'name' => 'Badge Updated',
            'description' => 'Updated deskripsi',
            'icon_url' => 'https://example.com/icon2.png',
            'tier' => 'silver',
            'condition_type' => 'post_count',
            'condition_value' => 10,
        ]);
        $response->assertStatus(200);

        // 4. Delete Badge
        $response = $this->deleteJson("/api/moderator/badges/{$badgeId}");
        $response->assertStatus(200);
    }

    public function test_moderator_can_view_single_report()
    {
        $this->actingAsUser(null, 'moderator');
        $post = \App\Models\Content\Post::factory()->create();
        $report = Report::create([
            'reporter_id' => $this->createUser()->id,
            'target_id' => $post->id,
            'target_type' => 'post',
            'reason' => 'Spam',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->getJson("/api/moderator/reports/{$report->id}");

        // Controller show() langsung return object tanpa wrapper 'data'
        $response->assertStatus(200)
            ->assertJsonPath('id', $report->id);
    }

    public function test_moderator_can_view_moderation_logs()
    {
        $this->actingAsUser(null, 'moderator');

        $response = $this->getJson('/api/moderator/logs');

        $response->assertStatus(200);
    }

    public function test_moderator_can_view_post_history()
    {
        $this->actingAsUser(null, 'moderator');
        $post = \App\Models\Content\Post::factory()->create();

        $response = $this->getJson("/api/moderator/posts/{$post->id}/history");

        $response->assertStatus(200);
    }

    public function test_moderator_can_view_comment_history()
    {
        $this->actingAsUser(null, 'moderator');
        $comment = \App\Models\Content\Comment::factory()->create();

        $response = $this->getJson("/api/moderator/comments/{$comment->id}/history");

        $response->assertStatus(200);
    }

    public function test_moderator_can_view_active_bans()
    {
        $this->actingAsUser(null, 'moderator');

        $response = $this->getJson('/api/moderator/bans');

        $response->assertStatus(200);
    }

    public function test_moderator_can_unban_user()
    {
        $this->actingAsUser(null, 'moderator');
        $targetUser = User::factory()->create(['is_banned' => true]);

        $response = $this->postJson("/api/moderator/bans/{$targetUser->id}/unban", [
            'reason' => 'Good behavior',
        ]);

        $response->assertStatus(200);
        $this->assertFalse($targetUser->fresh()->is_banned);
    }
}
