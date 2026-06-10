<?php

namespace Modules\Auth\F2_Login\Services;

use Modules\Auth\F2_Login\Repositories\LoginRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    protected LoginRepository $repository;

    public function __construct(LoginRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Validasi kredensial user untuk Sanctum SPA login.
     * Tidak membuat token — sesi dikelola melalui HttpOnly cookie.
     */
    public function validateCredentials(array $data)
    {
        $user = $this->repository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->is_banned) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah diblokir oleh moderator/admin.'],
            ]);
        }

        return $user;
    }
}