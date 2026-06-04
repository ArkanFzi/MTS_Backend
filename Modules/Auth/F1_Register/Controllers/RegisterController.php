<?php

namespace Modules\Auth\F1_Register\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Auth\F1_Register\Requests\RegisterRequest;
use Modules\Auth\F1_Register\Services\RegisterService;

class RegisterController extends Controller
{
    protected RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    // GANTI DI SINI: dari __invoke jadi register
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerService->execute($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil. Akun Anda telah siap!',
            'data'    => [
                'user' => [
                    'id'       => $result['user']->id,
                    'username' => $result['user']->username,
                    'email'    => $result['user']->email,
                    'level'    => $result['user']->level,
                ],
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type']
            ]
        ], 201);
    }
}