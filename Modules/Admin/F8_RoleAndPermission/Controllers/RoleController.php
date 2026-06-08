<?php

namespace Modules\Admin\F8_RoleAndPermission\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Role::all()
        ]);
    }
}
