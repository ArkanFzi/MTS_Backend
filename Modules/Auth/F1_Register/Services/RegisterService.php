<?php

namespace Modules\Auth\F1_Register\Services;

use Modules\Auth\F1_Register\Repositories\RegisterRepository;
use Illuminate\Validation\ValidationException;

class RegisterService
{
    protected RegisterRepository $registerRepository;

    public function __construct(RegisterRepository $registerRepository)
    {
        $this->registerRepository = $registerRepository;
    }

    public function execute(array $data): array
    {
        // Cek apakah role "user" sudah ada
        if (!\App\Models\Auth\Role::where('name', 'user')->exists()) {
            throw ValidationException::withMessages([
                'error' => ['Role "user" belum dibuat. Jalankan Role Seeder terlebih dahulu.']
            ]);
        }

        $user = $this->registerRepository->create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer'
        ];
    }
}