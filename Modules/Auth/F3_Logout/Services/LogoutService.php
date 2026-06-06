<?php

namespace Modules\Auth\F3_Logout\Services;

use App\Models\Auth\User;

class LogoutService
{
    /**
     * Logout dari sesi/perangkat saat ini.
     */
    public function logoutCurrentToken(User $user): bool
    {
        return $user->currentAccessToken()->delete();
    }
}
