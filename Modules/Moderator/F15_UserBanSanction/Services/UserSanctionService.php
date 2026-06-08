<?php

namespace Modules\Moderator\F15_UserBanSanction\Services;

use Modules\Moderator\F15_UserBanSanction\Repositories\UserSanctionRepository;
use Modules\Moderator\F14_ModeratorActionLog\Services\ModerationLogService;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User;

class UserSanctionService
{
    protected UserSanctionRepository $repository;
    protected ModerationLogService $logService;
    protected NotificationService $notificationService;
    protected BadgeAchievementService $gamification;

    public function __construct(
        UserSanctionRepository $repository,
        ModerationLogService $logService,
        NotificationService $notificationService,
        BadgeAchievementService $gamification
    ) {
        $this->repository = $repository;
        $this->logService = $logService;
        $this->notificationService = $notificationService;
        $this->gamification = $gamification;
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

        $this->notificationService->createNotification(
            $id,
            Auth::id(),
            'user_banned',
            $id,
            User::class
        );

        // Kurangi poin saat di-ban
        $user = User::find($id);
        if ($user) {
            $this->gamification->addPoints($user, -50, 'banned', $id, 'Akun di-ban oleh moderator');
        }

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
        $this->logService->logAction([
            'moderator_id'   => Auth::id(),
            'target_user_id' => $id,
            'action_type'    => 'warn_user',
            'reason'         => $data['reason'],
            'notes'          => $data['notes'] ?? null,
        ]);

        $this->notificationService->createNotification(
            $id,
            Auth::id(),
            'user_warned',
            $id,
            User::class
        );

        // Kurangi poin saat di-warn
        $user = User::find($id);
        if ($user) {
            $this->gamification->addPoints($user, -15, 'warned', $id, 'Mendapat peringatan dari moderator');
        }

        return true;
    }
}