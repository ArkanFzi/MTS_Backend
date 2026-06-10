<?php

namespace Modules\Auth\F1_Register\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        $user = $this->registerService->execute($request->validated());

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil. Selamat datang!',
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
            ]
        ], 201);
    }
}