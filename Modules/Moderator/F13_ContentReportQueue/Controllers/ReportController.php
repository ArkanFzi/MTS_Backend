<?php

namespace Modules\Admin\F13_ContentReportQueue\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\F13_ContentReportQueue\Services\ReportService;
use Modules\Admin\F13_ContentReportQueue\Requests\UpdateReportRequest;

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
        return view('admin::F13_ContentReportQueue.index', compact('reports'));
    }

    public function show(string $id)
    {
        $report = $this->service->findById($id);
        return view('admin::F13_ContentReportQueue.show', compact('report'));
    }

    public function update(UpdateReportRequest $request, string $id)
    {
        $this->service->resolve($id, $request->validated());
        return redirect()->route('admin.reports.index')
                         ->with('success', 'Laporan berhasil diproses.');
    }
}