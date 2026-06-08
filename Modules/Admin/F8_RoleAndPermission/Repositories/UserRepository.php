<?php

namespace Modules\Admin\F8_RoleAndPermission\Repositories;

use App\Models\Auth\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Support\Str;

class UserRepository
{
    public function __construct(
        protected User $model,
        protected NotificationService $notificationService
    ) {}

    public function all(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('roles')->paginate($perPage);
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

        $user->roles()->sync([$roleId]);

        // Kirim notifikasi ke user yang di-assign role
        $this->notificationService->createNotification(
            userId  : $userId,
            actorId : auth()->id(),
            type    : 'role_assigned',
            refId   : $roleId,
            refType : 'role'
        );

        return true;
    }
}