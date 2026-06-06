<?php

namespace Modules\User\F30_UserReport\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F30_UserReport\Requests\StoreReportRequest;
use Modules\User\F30_UserReport\Services\UserReportService;
use Illuminate\Http\JsonResponse;

class UserReportController extends Controller
{
    protected $reportService;

    public function __construct(UserReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = $this->reportService->handleReport($request->validated());

        return response()->json([
            'message' => 'Report submitted successfully.',
            'data'    => $report
        ], 201);
    }
}