<?php

namespace Modules\Auth\F31_ForgotPassword\Repositories;

use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class ForgotPasswordRepository
{
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function upsertToken(string $email, string $token): void
    {
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
    }

    public function findToken(string $email, string $token): ?object
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();
    }

    public function deleteToken(string $email): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
    }
}