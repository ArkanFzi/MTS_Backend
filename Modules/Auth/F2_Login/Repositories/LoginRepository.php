<?php

namespace Modules\Auth\F2_Login\Repositories;

use App\Models\Auth\User;

class LoginRepository
{
    /**
     * Cari user berdasarkan email atau username
     */
    public function findByEmailOrUsername(string $login): ?User
    {
        return User::where('email', $login)
                   ->orWhere('username', $login)
                   ->first();
    }
}