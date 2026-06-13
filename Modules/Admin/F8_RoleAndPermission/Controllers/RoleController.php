<?php

namespace Modules\Admin\F8_RoleAndPermission\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => Role::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
        ]);

        $role = Role::create([
            'id'          => (string) Str::uuid(),
            'name'        => strtolower($request->name),
            'permissions' => $request->permissions ?? [],
            'created_at'  => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data'    => $role,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $id,
        ]);

        $role->update([
            'name'        => strtolower($request->name),
            'permissions' => $request->permissions ?? $role->permissions,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data'    => $role->fresh(),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
        }

        if (in_array($role->name, ['admin', 'moderator', 'user'])) {
            return response()->json(['success' => false, 'message' => 'Cannot delete system roles.'], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }
}
