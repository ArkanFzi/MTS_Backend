<?php

namespace Modules\Moderator\F15_UserBanSanction\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Moderator\F15_UserBanSanction\Services\UserSanctionService;
use Modules\Moderator\F15_UserBanSanction\Requests\BanUserRequest;
use Modules\Moderator\F15_UserBanSanction\Requests\WarnUserRequest;

class UserSanctionController extends Controller
{
    protected UserSanctionService $service;

    public function __construct(UserSanctionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $users = $this->service->getAllPaginated(15, $request->search);
        return view('admin::F15_UserBanSanction.index', compact('users'));
    }

    public function ban(BanUserRequest $request, string $id): JsonResponse
    {
        $this->service->banUser($id, $request->validated());
        return response()->json(['message' => 'User berhasil di-ban.']);
    }

    public function unban(Request $request, string $id): JsonResponse
    {
        $this->service->unbanUser($id, $request->reason);
        return response()->json(['message' => 'User berhasil di-unban.']);
    }

    public function warn(WarnUserRequest $request, string $id): JsonResponse
    {
        $this->service->warnUser($id, $request->validated());
        return response()->json(['message' => 'Warning berhasil dikirim ke user.']);
    }

}