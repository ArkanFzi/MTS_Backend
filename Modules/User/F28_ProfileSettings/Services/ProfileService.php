<?php

namespace Modules\User\F28_ProfileSettings\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Modules\User\F26_NotificationSystem\Services\NotificationService;

class ProfileService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function updateProfile(User $user, array $data): User
    {
        $wasIncomplete = empty($user->bio) || empty($user->avatar_url);
        
        $user->update($data);
        $updatedUser = $user->fresh();

        // Cek apakah baru saja melengkapi profile
        if ($wasIncomplete && (!empty($updatedUser->bio) && !empty($updatedUser->avatar_url))) {
            $this->notificationService->createNotification(
                $user->id,
                $user->id,
                'profile_completed',
                $user->id,
                User::class
            );
        }

        return $updatedUser;
    }

    public function updatePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (!Hash::check($oldPassword, $user->password_hash)) {
            throw new \Exception("Password lama tidak sesuai.");
        }

        $user->update(['password_hash' => Hash::make($newPassword)]);
    }
}
