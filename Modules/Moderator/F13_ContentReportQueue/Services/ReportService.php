<?php

namespace Modules\Moderator\F13_ContentReportQueue\Services;

use Modules\Moderator\F13_ContentReportQueue\Repositories\ReportRepository;
use Modules\Moderator\F15_UserBanSanction\Services\UserSanctionService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use App\Models\Moderation\ModerationLog;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    protected ReportRepository $repository;
    protected BadgeAchievementService $gamification;
    protected UserSanctionService $sanctionService;
    protected NotificationService $notificationService;

    public function __construct(
        ReportRepository $repository,
        BadgeAchievementService $gamification,
        UserSanctionService $sanctionService,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->gamification = $gamification;
        $this->sanctionService = $sanctionService;
        $this->notificationService = $notificationService;
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

        // Notifikasi otomatis ke pelapor
        $this->notificationService->createNotification(
            userId: $report->reporter_id,
            actorId: Auth::id(),
            type: 'report_updated',
            refId: $report->id,
            refType: 'report'
        );

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

        if ($data['status'] === 'resolved' && $targetUserId) {
            $user = User::find($targetUserId);
            if ($user) {
                $this->gamification->addPoints(
                    $user,
                    -10,
                    'report_accepted',
                    $id,
                    'Laporan terhadap konten diterima'
                );

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