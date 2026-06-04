<?php

namespace Modules\Admin\F9_UserManagement\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\F9_UserManagement\Repositories\UserAdminRepository;

class UserAdminService
{
    protected UserAdminRepository $repo;

    public function __construct(UserAdminRepository $repo)
    {
        $this->repo = $repo;
    }

    public function updateProfile(string $id, array $data): User
    {
        $user = $this->repo->findById($id);
        if (!$user) throw new \Exception("User tidak ditemukan.", 404);
        $this->repo->update($user, $data);
        return $user->fresh();
    }

    public function resetPassword(string $id, string $newPassword): void
    {
        $user = $this->repo->findById($id);
        if (!$user) throw new \Exception("User tidak ditemukan.", 404);
        $this->repo->update($user, ['password_hash' => Hash::make($newPassword)]);
    }
}