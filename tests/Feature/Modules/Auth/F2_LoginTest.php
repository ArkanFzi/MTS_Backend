<?php

namespace Tests\Feature\Modules\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;

class F2_LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create(['password_hash' => bcrypt('password123')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'message', 'data' => ['access_token']]);
    }
}
