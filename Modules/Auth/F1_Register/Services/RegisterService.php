<?php

namespace Modules\Auth\F1_Register\Services;

use Modules\Auth\F1_Register\Repositories\RegisterRepository;

class RegisterService
{
    protected RegisterRepository $registerRepository;

    public function __construct(RegisterRepository $registerRepository)
    {
        $this->registerRepository = $registerRepository;
    }

    public function execute(array $data): array
    {
        // 1. Simpan data user baru lewat repository
        $user = $this->registerRepository->createUser($data);

        // 2. Buat token akses menggunakan Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer'
        ];
    }
}