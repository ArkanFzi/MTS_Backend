<?php

namespace Modules\Moderator\F15_UserBanSanction\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Moderator\F15_UserBanSanction\Services\UserSanctionService;
use Modules\Moderator\F15_UserBanSanction\Requests\BanUserRequest;

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

    public function ban(BanUserRequest $request, string $id)
    {
        $this->service->banUser($id, $request->validated());
        return redirect()->route('admin.bans.index')
                         ->with('success', 'User berhasil di-ban.');
    }

    public function unban(Request $request, string $id)
    {
        $this->service->unbanUser($id, $request->reason);
        return redirect()->route('admin.bans.index')
                         ->with('success', 'User berhasil di-unban.');
    }
}