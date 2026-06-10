<?php

namespace Tests\Feature\Modules\Moderator;

use App\Models\Auth\User;
use App\Models\Content\Post;
use App\Models\Moderation\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F13_ContentReportQueueTest extends TestCase
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
    public function moderator_can_view_reports()
    {
        $response = $this->actingAs($this->moderator, 'sanctum')
                         ->getJson('/api/moderator/reports');

        $response->assertStatus(200);
    }
}
