<?php

namespace Modules\Moderator\F13_ContentReportQueue\Controllers;

use App\Http\Controllers\Controller; // Gunakan base controller Laravel
use Illuminate\Http\Request;
use Modules\Moderator\F13_ContentReportQueue\Services\ReportService;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $reports = $this->service->getAllPaginated(20, $request->status, $request->search);
        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }

    public function show(string $id)
    {
        $report = $this->service->findById($id);
        return response()->json([
            'success' => true,
            'data'    => $report,
        ]);
    }

    public function update(\Modules\Moderator\F13_ContentReportQueue\Requests\UpdateReportRequest $request, string $id)
    {
        $this->service->resolve($id, $request->validated());
        return response()->json(['message' => 'Laporan berhasil diproses.']);
    }
}