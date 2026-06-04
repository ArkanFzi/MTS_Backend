<?php

namespace Modules\Admin\F8_RoleAndPermission\Controllers;

use App\Http\Controllers\Controller;
use Modules\Admin\F8_RoleAndPermission\Services\RoleService;
use Modules\Admin\F8_RoleAndPermission\Requests\StoreRoleRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAll();
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $role = $this->roleService->find($id);
        
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    public function update(StoreRoleRequest $request, string $id): JsonResponse
    {
        $success = $this->roleService->update($id, $request->validated());

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $success = $this->roleService->delete($id);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}