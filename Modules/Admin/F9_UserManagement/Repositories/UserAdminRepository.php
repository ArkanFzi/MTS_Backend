<?php

namespace Modules\Admin\F9_UserManagement\Repositories;

use App\Models\Auth\User;

class UserAdminRepository
{
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}