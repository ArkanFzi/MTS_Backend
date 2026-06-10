<?php

namespace Tests;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a user with a specific role.
     */
    protected function createUser(array $attributes = [], string $role = 'user'): User
    {
        $user = User::factory()->create($attributes);
        
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel) {
            $user->roles()->attach($roleModel->id, ['assigned_at' => now()]);
        }

        return $user;
    }

    /**
     * Create an admin user.
     */
    protected function createAdmin(array $attributes = []): User
    {
        return $this->createUser($attributes, 'admin');
    }

    /**
     * Create a moderator user.
     */
    protected function createModerator(array $attributes = []): User
    {
        return $this->createUser($attributes, 'moderator');
    }

    /**
     * Authenticate as a user using Sanctum.
     */
    protected function actingAsUser($userOrAttributes = null, string $role = 'user'): User
    {
        if ($userOrAttributes instanceof User) {
            $user = $userOrAttributes;
        } else {
            $user = $this->createUser($userOrAttributes ?? [], $role);
        }

        Sanctum::actingAs($user);
        return $user;
    }
}
