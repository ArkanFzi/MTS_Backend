<?php

namespace Modules\Admin\F8_RoleAndPermission\Repositories;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('roles')
            ->paginate($perPage);
    }

    public function find(string $id): ?User
    {
        return $this->model->with(['roles'])->find($id);
    }

    public function update(string $id, array $data): bool
    {
        $user = $this->find($id);
        return $user ? $user->update($data) : false;
    }

    public function resetPassword(string $id, string $newPassword): bool
    {
        $user = $this->find($id);
        if (!$user) return false;

        $user->password_hash = bcrypt($newPassword);
        return $user->save();
    }

    public function assignRole(string $userId, string $roleId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;

        return $user->roles()->syncWithoutDetaching([$roleId]);
    }

    public function removeRole(string $userId, string $roleId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;

        return $user->roles()->detach($roleId);
    }
}