<?php

namespace Modules\Auth\F3_Logout\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\F3_Logout\Services\LogoutService;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(
        protected LogoutService $logoutService
    ) {}

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\Auth\User $user */
        $user = $request->user();

        $this->logoutService->logoutCurrentToken($user);

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil logout, token telah dihapus.'
        ], 200);
    }
}