<?php

namespace Modules\Moderator\F13_ContentReportQueue\Services;

use Modules\Moderator\F13_ContentReportQueue\Repositories\ReportRepository;
use Modules\Moderator\F15_UserBanSanction\Services\UserSanctionService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use App\Models\Moderation\ModerationLog;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    protected ReportRepository $repository;
    protected BadgeAchievementService $gamification;
    protected UserSanctionService $sanctionService;

    public function __construct(
        ReportRepository $repository,
        BadgeAchievementService $gamification,
        UserSanctionService $sanctionService
    ) {
        $this->repository = $repository;
        $this->gamification = $gamification;
        $this->sanctionService = $sanctionService;
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

        $this->repository->update($id, [
            'status'      => $data['status'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        $target = $report->target;
        $targetUserId = $target ? ($target->user_id ?? null) : null;

        ModerationLog::create([
            'moderator_id'   => Auth::id(),
            'target_user_id' => $targetUserId,
            'action_type'    => 'resolve_report',
            'reason'         => $data['reason'] ?? 'Laporan ditangani',
            'notes'          => $data['notes'] ?? null,
            'created_at'     => now(),
        ]);

        // Hanya proses jika status resolved dan ada target user
        if ($data['status'] === 'resolved' && $targetUserId) {
            $user = User::find($targetUserId);
            if ($user) {
                // Kurangi poin
                $this->gamification->addPoints(
                    $user,
                    -10,
                    'report_accepted',
                    $id,
                    'Laporan terhadap konten diterima'
                );

                // Eksekusi action
                $action = $data['action'] ?? 'none';

                if ($action === 'warn') {
                    $this->sanctionService->warnUser($targetUserId, [
                        'reason' => $data['reason'] ?? 'Peringatan dari laporan yang diterima',
                        'notes'  => $data['notes'] ?? null,
                    ]);
                } elseif ($action === 'ban') {
                    $this->sanctionService->banUser($targetUserId, [
                        'reason' => $data['reason'] ?? 'Ban dari laporan yang diterima',
                        'notes'  => $data['notes'] ?? null,
                    ]);
                }
            }
        }

        return true;
    }
}