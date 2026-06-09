<?php

namespace Modules\Auth\F2_Login\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // 1. Cek kredensial dan buat Session Cookie secara otomatis
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email atau password salah.'
                ], 401);
            }

            // 2. Mencegah Session Fixation Attack (Wajib untuk keamanan SPA)
            $request->session()->regenerate();

            // 3. Ambil data user yang sedang login
            $user = Auth::user();

            // Opsional: Jika kamu masih butuh logic tambahan dari service (misal: catat log login)
            // $this->loginService->execute($user); 

            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil!',
                'data'    => [
                    'user' => [
                        'id'                => $user->id,
                        'username'          => $user->username,
                        'email'             => $user->email,
                        'avatar_url'        => $user->avatar_url,
                        'level'             => $user->level,
                        'reputation_points' => $user->reputation_points,
                        'is_banned'         => $user->is_banned,
                        'roles'             => $user->roles->pluck('name'),
                    ],
                    // HAPUS access_token dan token_type dari sini
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        }
    }
}