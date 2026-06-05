<?php

namespace Modules\User\F28_ProfileSettings\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function updatePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (!Hash::check($oldPassword, $user->password_hash)) {
            throw new \Exception("Password lama tidak sesuai.");
        }

        $user->update(['password_hash' => Hash::make($newPassword)]);
    }
}
