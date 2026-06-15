<?php

namespace Modules\Auth\F3_Logout\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function logout(Request $request): JsonResponse
    {
        // Cookie/session logout (untuk web browser)
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        auth()->guard('web')->logout();

        // Revoke Bearer token (untuk Tauri desktop app)
        if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil logout.'
        ], 200);
    }
}