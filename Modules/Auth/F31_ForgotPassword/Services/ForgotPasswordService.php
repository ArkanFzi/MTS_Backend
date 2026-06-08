<?php

namespace Modules\Auth\F31_ForgotPassword\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\F31_ForgotPassword\Jobs\SendResetPasswordEmailJob;
use Modules\Auth\F31_ForgotPassword\Repositories\ForgotPasswordRepository;

class ForgotPasswordService
{
    public function __construct(
        protected ForgotPasswordRepository $repo
    ) {}

    public function sendResetLink(string $email): void
    {
        $user = $this->repo->findUserByEmail($email);

        // Tidak throw error meski email tidak ada (anti email enumeration)
        if (!$user) return;

        $token    = Str::random(64);
        $resetUrl = config('app.frontend_url')
            . '/reset-password?token=' . $token
            . '&email=' . urlencode($email);

        $this->repo->upsertToken($email, $token);

        SendResetPasswordEmailJob::dispatch($email, $resetUrl);
    }

    public function resetPassword(string $email, string $token, string $newPassword): void
    {
        $record = $this->repo->findToken($email, $token);

        if (!$record) {
            throw new \Exception("Token tidak valid.", 400);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            $this->repo->deleteToken($email);
            throw new \Exception("Token sudah expired.", 400);
        }

        $user = $this->repo->findUserByEmail($email);
        if (!$user) throw new \Exception("User tidak ditemukan.", 404);

        $user->update(['password_hash' => Hash::make($newPassword)]);
        $this->repo->deleteToken($email);
    }
}