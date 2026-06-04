<?php

namespace Modules\Auth\F1_Register\Repositories;

use App\Models\Auth\User;

class RegisterRepository
{
    public function createUser(array $data): User
    {
        return User::create([
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => bcrypt($data['password']), // Sesuai kolom database: password_hash
            'bio'           => $data['bio'] ?? null,
        ]);
    }
}