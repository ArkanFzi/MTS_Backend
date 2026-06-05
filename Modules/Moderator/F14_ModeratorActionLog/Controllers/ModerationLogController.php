<?php

namespace Modules\Moderator\F14_ModeratorActionLog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Moderator\F14_ModeratorActionLog\Services\ModerationLogService;

class ModerationLogController extends Controller
{
    protected ModerationLogService $service;

    public function __construct(ModerationLogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $logs = $this->service->getAllPaginated(
            20, 
            $request->action_type, 
            $request->search
        );

        return view('admin::F14_ModeratorActionLog.index', compact('logs'));
    }
}