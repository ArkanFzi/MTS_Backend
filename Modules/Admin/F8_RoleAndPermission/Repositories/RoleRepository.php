<?php

namespace Modules\Admin\F8_RoleAndPermission\Repositories;

use App\Models\Auth\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    protected Role $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('users')->get();
    }

    public function find(string $id): ?Role
    {
        return $this->model->with('users')->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $role = $this->find($id);
        return $role ? $role->update($data) : false;
    }

    public function delete(string $id): bool
    {
        $role = $this->find($id);
        return $role ? $role->delete() : false;
    }

    public function assignPermissions(string $id, array $permissions): bool
    {
        $role = $this->find($id);
        if (!$role) return false;

        $role->permissions = $permissions;
        return $role->save();
    }
}