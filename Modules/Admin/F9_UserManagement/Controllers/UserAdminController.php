<?php

namespace Modules\Admin\F9_UserManagement\Controllers;

use App\Http\Controllers\Controller;
use Modules\Admin\F9_UserManagement\Requests\UpdateProfileRequest;
use Modules\Admin\F9_UserManagement\Requests\ResetPasswordRequest;
use Modules\Admin\F9_UserManagement\Services\UserAdminService;

class UserAdminController extends Controller
{
    protected UserAdminService $service;

    public function __construct(UserAdminService $service)
    {
        $this->service = $service;
    }

    public function updateProfile(UpdateProfileRequest $request, string $id)
    {
        $user = $this->service->updateProfile($id, $request->validated());
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function resetPassword(ResetPasswordRequest $request, string $id)
    {
        $this->service->resetPassword($id, $request->password);
        return response()->json(['status' => 'success', 'message' => 'Password di-reset.']);
    }
}