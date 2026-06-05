<?php

namespace Modules\Auth\F1_Register\Repositories;

use App\Models\Auth\User;
use App\Models\Auth\Role;

class RegisterRepository
{
    public function create(array $data): User
    {
        $user = User::create([
            'username'          => $data['username'],
            'email'             => $data['email'],
            'password_hash'     => bcrypt($data['password']),
            'bio'               => $data['bio'] ?? null,
            'reputation_points' => 0,
            'level'             => 1,
            'is_banned'         => false,
        ]);

        // Assign default role "user"
        $userRole = Role::where('name', 'user')->first();

        if ($userRole) {
            $user->roles()->attach($userRole->id, [
                'assigned_at' => now()
            ]);
        }

        // Load roles supaya langsung tersedia di response
        $user->load('roles');

        return $user;
    }
}