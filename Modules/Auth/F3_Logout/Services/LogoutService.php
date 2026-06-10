<?php

namespace Modules\Auth\F3_Logout\Services;

use Illuminate\Http\Request;

class LogoutService
{
    /**
     * Invalidate sesi saat ini (Sanctum SPA cookie-based).
     * Menghapus session dari database dan regenerate CSRF token.
     */
    public function invalidateSession(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        auth()->guard('web')->logout();
    }
}
