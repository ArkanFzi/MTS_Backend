<?php

namespace Modules\Auth\F2_Login\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function execute(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        // FIX DI SINI: ganti $user->password menjadi $user->password_hash
        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Generate token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer'
        ];
    }
}