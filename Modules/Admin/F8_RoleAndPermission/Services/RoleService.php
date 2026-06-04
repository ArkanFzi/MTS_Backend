<?php

namespace Modules\Admin\F8_RoleAndPermission\Services;

use Modules\Admin\F8_RoleAndPermission\Repositories\RoleRepository;
use App\Models\Auth\Role;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(
        protected RoleRepository $roleRepository
    ) {}

    public function getAll()
    {
        return $this->roleRepository->all();
    }

    public function find(string $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function create(array $data): Role
    {
        // Business logic
        if (isset($data['name'])) {
            $data['name'] = strtolower(trim($data['name']));
        }

        return $this->roleRepository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        if (isset($data['name'])) {
            $data['name'] = strtolower(trim($data['name']));
        }

        return $this->roleRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        // Prevent deleting admin role (business rule)
        $role = $this->roleRepository->find($id);
        if ($role && $role->name === 'admin') {
            throw new \Exception('Cannot delete admin role');
        }

        return $this->roleRepository->delete($id);
    }

    public function assignPermissions(string $id, array $permissions): bool
    {
        return $this->roleRepository->assignPermissions($id, $permissions);
    }
}