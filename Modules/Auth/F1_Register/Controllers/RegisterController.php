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

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerService->execute($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil. Selamat datang!',
            'data'    => [
                'user' => [
                    'id'                => $result['user']->id,
                    'username'          => $result['user']->username,
                    'email'             => $result['user']->email,
                    'avatar_url'        => $result['user']->avatar_url,
                    'level'             => $result['user']->level,
                    'reputation_points' => $result['user']->reputation_points,
                    'roles'             => $result['user']->roles->pluck('name'), // contoh: ["user"]
                ],
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type']
            ]
        ], 201);
    }
}