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
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- Proteksi Role ---
        if ($targetUser->isAdmin()) {
            abort(403, 'Anda tidak dapat memblokir seorang Administrator.');
        }

        if ($currentUser->isModerator() && $targetUser->isModerator()) {
            abort(403, 'Seorang Moderator tidak dapat memblokir sesama Moderator.');
        }

        $this->repository->updateBanStatus($id, true);

        $log = $this->logService->logAction([
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
            $log->id,
            'moderation_log'
        );

        // Kurangi poin saat di-ban
        $this->gamification->addPoints($targetUser, -20, 'banned', $id, 'Akun di-ban oleh moderator');

        return true;
    }

    public function unbanUser(string $id, string $reason = null)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- Proteksi Role ---
        if ($targetUser->isAdmin()) {
            abort(403, 'Status Administrator tidak dapat diubah melalui menu ini.');
        }

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
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- Proteksi Role ---
        if ($targetUser->isAdmin()) {
            abort(403, 'Anda tidak dapat memberikan peringatan kepada seorang Administrator.');
        }

        if ($currentUser->isModerator() && $targetUser->isModerator()) {
            abort(403, 'Seorang Moderator tidak dapat memberikan peringatan kepada sesama Moderator.');
        }

        $log = $this->logService->logAction([
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
            $log->id,
            'moderation_log'
        );

        // Kurangi poin saat di-warn
        $this->gamification->addPoints($targetUser, -5, 'warned', $id, 'Mendapat peringatan dari moderator');

        return true;
    }
}