<?php

namespace Modules\Admin\F8_RoleAndPermission\Controllers;

use App\Http\Controllers\Controller;
use Modules\Admin\F8_RoleAndPermission\Services\UserManagementService;
use Modules\Admin\F8_RoleAndPermission\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserManagementService $userService
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userService->findUser($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user->load('roles')
        ]);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
{
    $user = $this->userService->findUser($id);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    // Cari role berdasarkan nama
    $role = \App\Models\Auth\Role::where('name', $request->role)->first();

    if (!$role) {
        return response()->json(['success' => false, 'message' => 'Role not found'], 404);
    }

    // Sync role (replace semua role lama dengan role baru)
    $this->userService->assignRoleToUser($id, $role->id);

    return response()->json([
        'success' => true,
        'message' => "User role updated to {$request->role}"
    ]);
}
}