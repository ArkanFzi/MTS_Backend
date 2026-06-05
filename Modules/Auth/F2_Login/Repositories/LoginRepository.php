<?php

namespace Modules\Auth\F2_Login\Repositories;

use App\Models\Auth\User;

class LoginRepository
{
    /**
     * Cari user berdasarkan email + load roles
     */
    public function findByEmail(string $email): ?User
    {
        return User::with('roles')
                    ->where('email', $email)
                    ->first();
    }
}