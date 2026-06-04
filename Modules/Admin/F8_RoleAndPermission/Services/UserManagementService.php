<?php

namespace Modules\Admin\F8_RoleAndPermission\Services;

use Modules\Admin\F8_RoleAndPermission\Repositories\UserRepository;
use App\Models\Auth\User;

class UserManagementService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getAllUsers(int $perPage = 15)
    {
        return $this->userRepository->all($perPage);
    }

    public function findUser(string $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function updateUser(string $id, array $data): bool
    {
        // Business logic: cannot ban admin
        if (isset($data['is_banned']) && $data['is_banned']) {
            $user = $this->userRepository->find($id);
            if ($user && $user->roles->contains('name', 'admin')) {
                throw new \Exception('Cannot ban administrator account');
            }
        }

        return $this->userRepository->update($id, $data);
    }

    public function resetPassword(string $id, string $newPassword): bool
    {
        return $this->userRepository->resetPassword($id, $newPassword);
    }

    public function assignRoleToUser(string $userId, string $roleId): bool
    {
        return $this->userRepository->assignRole($userId, $roleId);
    }

    public function removeRoleFromUser(string $userId, string $roleId): bool
    {
        return $this->userRepository->removeRole($userId, $roleId);
    }
}