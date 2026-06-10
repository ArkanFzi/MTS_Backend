<?php

namespace Tests\Feature\Modules\Moderator;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F14_ModeratorActionLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_moderator_action_log()
    {
        $moderator = User::factory()->create();
        $moderator->roles()->create(['name' => 'moderator']);

        $response = $this->actingAs($moderator, 'sanctum')->getJson('/api/moderator/logs');
        $response->assertStatus(200);
    }
}
