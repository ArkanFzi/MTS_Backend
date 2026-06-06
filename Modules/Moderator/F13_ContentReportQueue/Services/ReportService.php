<?php

namespace Modules\Moderator\F13_ContentReportQueue\Services;

use Modules\Moderator\F13_ContentReportQueue\Repositories\ReportRepository;
use App\Models\Moderation\ModerationLog;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    protected ReportRepository $repository;

    public function __construct(ReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPaginated(int $perPage = 20, $status = null, $search = null)
    {
        return $this->repository->getAllPaginated($perPage, $status, $search);
    }

    public function findById(string $id)
    {
        return $this->repository->findById($id);
    }

    public function resolve(string $id, array $data)
    {
        $report = $this->findById($id);
        if (!$report) return false;

        $updateData = [
            'status'       => $data['status'],
            'resolved_by'  => Auth::id(),
            'resolved_at'  => now(),
        ];

        $this->repository->update($id, $updateData);

        // Ambil target user ID dari model target (post/comment)
        $target = $report->target;
        $targetUserId = $target ? ($target->user_id ?? null) : null;

        // Catat ke Moderation Log
        ModerationLog::create([
            'moderator_id'   => Auth::id(),
            'target_user_id' => $targetUserId, 
            'action_type'    => 'resolve_report',
            'reason'         => $data['reason'] ?? 'Laporan ditangani',
            'notes'          => $data['notes'] ?? null,
            'created_at'     => now(),
        ]);

        return true;
    }
}