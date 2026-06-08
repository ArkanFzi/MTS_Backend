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

    public function assignRoleToUser(string $userId, string $roleId): bool
    {
        return $this->userRepository->assignRole($userId, $roleId);
    }
}