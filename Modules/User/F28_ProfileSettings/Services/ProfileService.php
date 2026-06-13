<?php

namespace Modules\User\F28_ProfileSettings\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        if (isset($data['avatar'])) {
            // 1. Ambil path relatif file lama secara aman menggunakan parse_url
            if ($user->avatar_url) {
                $cleanPath = parse_url($user->avatar_url, PHP_URL_PATH); // Menghasilkan '/storage/avatars/xxx.png'
                $oldPath = str_replace('/storage/', '', $cleanPath);     // Menghasilkan 'avatars/xxx.png'
                Storage::disk('public')->delete($oldPath);
            }

            // 2. Simpan file baru ke storage
            $path = $data['avatar']->store('avatars', 'public');
            
            // 3. Pakai asset() untuk memastikan mengembalikan FULL URL (termasuk http://localhost:8000)
            $user->avatar_url = asset('storage/' . $path);
            unset($data['avatar']);
        }

        // 4. Gunakan fill + save secara manual untuk menghindari jebakan $fillable pada Model User
        $user->fill($data);
        $user->save();
        
        $updatedUser = $user->fresh();

        if ($wasIncomplete && (!empty($updatedUser->bio) && !empty($updatedUser->avatar_url))) {
            $this->notificationService->createNotification(
                $user->id,
                $user->id,
                'profile_completed',
                $user->id,
                'user'
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