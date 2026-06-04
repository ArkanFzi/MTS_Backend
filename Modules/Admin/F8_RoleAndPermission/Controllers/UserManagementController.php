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
        $success = $this->userService->updateUser($id, $request->validated());

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    public function resetPassword(string $id): JsonResponse
    {
        $request = request();
        $newPassword = $request->input('password');

        if (empty($newPassword) || strlen($newPassword) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Password must be at least 8 characters'
            ], 422);
        }

        $success = $this->userService->resetPassword($id, $newPassword);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully'
        ]);
    }
}