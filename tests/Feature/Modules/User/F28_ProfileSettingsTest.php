<?php

namespace Tests\Feature\Modules\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F28_ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_test_profile_settings()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/settings/profile');
        $response->assertStatus(200);
    }
}
