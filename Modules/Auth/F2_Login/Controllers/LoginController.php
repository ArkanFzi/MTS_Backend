<?php

namespace Modules\Auth\F2_Login\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Modules\Auth\F2_Login\Requests\LoginRequest;
use Modules\Auth\F2_Login\Services\LoginService;

class LoginController extends Controller
{
    protected LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    // GANTI DI SINI: dari __invoke jadi login
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginService->execute($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil!',
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
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Autentikasi gagal',
                'errors'  => $e->errors()
            ], 422);
        }
    }
}