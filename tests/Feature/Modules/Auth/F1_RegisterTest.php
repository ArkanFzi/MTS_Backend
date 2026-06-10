<?php

namespace Tests\Feature\Modules\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class F1_RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        // Pastikan role 'user' ada
        \App\Models\Auth\Role::firstOrCreate(['name' => 'user']);

        $response = $this->postJson('/api/auth/register', [
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'tester@example.com']);
    }
}
