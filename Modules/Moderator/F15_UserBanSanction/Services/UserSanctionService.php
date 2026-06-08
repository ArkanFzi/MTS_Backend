<?php

namespace Modules\Moderator\F15_UserBanSanction\Services;

use Modules\Moderator\F15_UserBanSanction\Repositories\UserSanctionRepository;
use Modules\Moderator\F14_ModeratorActionLog\Services\ModerationLogService;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class UserSanctionService
{
    protected UserSanctionRepository $repository;
    protected ModerationLogService $logService;
    protected NotificationService $notificationService;

    public function __construct(
        UserSanctionRepository $repository, 
        ModerationLogService $logService,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->logService = $logService;
        $this->notificationService = $notificationService;
    }

    public function getAllPaginated(int $perPage = 15, $search = null)
    {
        return $this->repository->getAllPaginated($perPage, $search);
    }

    public function findById(string $id)
    {
        return $this->repository->findById($id);
    }

    public function banUser(string $id, array $data)
    {
        $this->repository->updateBanStatus($id, true);

        $this->logService->logAction([
            'moderator_id'   => Auth::id(),
            'target_user_id' => $id,
            'action_type'    => 'ban_user',
            'reason'         => $data['reason'],
            'notes'          => $data['notes'] ?? null,
        ]);

        // Kirim Notifikasi Ban
        $this->notificationService->createNotification(
            $id,
            Auth::id(),
            'user_banned',
            $id,
            \App\Models\Auth\User::class
        );

        return true;
    }

    public function unbanUser(string $id, string $reason = null)
    {
        $this->repository->updateBanStatus($id, false);

        $this->logService->logAction([
            'moderator_id'   => Auth::id(),
            'target_user_id' => $id,
            'action_type'    => 'unban_user',
            'reason'         => $reason ?? 'User telah diunban',
            'notes'          => null,
        ]);

        return true;
    }

    public function warnUser(string $id, array $data)
{
    // Log aksi warning
    $this->logService->logAction([
        'moderator_id'   => Auth::id(),
        'target_user_id' => $id,
        'action_type'    => 'warn_user',
        'reason'         => $data['reason'],
        'notes'          => $data['notes'] ?? null,
    ]);

    // Kirim notifikasi warning ke user
    $this->notificationService->createNotification(
        $id,
        Auth::id(),
        'user_warned',
        $id,
        \App\Models\Auth\User::class
    );

    return true;
}
}
