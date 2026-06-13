<?php

namespace Modules\Auth\F2_Login\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\F2_Login\Requests\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.'
            ], 401);
        }

        $user = Auth::user();

        if ($user->is_banned) {
            Auth::logout();
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda telah diblokir oleh moderator/admin.'
            ], 403);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $data = [
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
        ];

        // Token hanya untuk environment local/testing (Cypress)
        // Cookie session SPA tetap berjalan normal
        if (app()->environment('local', 'testing')) {
            $data['token'] = $user->createToken('cypress')->plainTextToken;
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil!',
            'data'    => $data
        ], 200);
    }
}