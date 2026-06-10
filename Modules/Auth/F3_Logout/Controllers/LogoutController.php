<?php

namespace Modules\Auth\F3_Logout\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function logout(Request $request): JsonResponse
    {
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        auth()->guard('web')->logout();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil logout.'
        ], 200);
    }
}