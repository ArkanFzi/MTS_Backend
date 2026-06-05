<?php

namespace Modules\Admin\F15_UserBanSanction\Services;

use Modules\Admin\F15_UserBanSanction\Repositories\UserSanctionRepository;
use Modules\Admin\F14_ModeratorActionLog\Services\ModerationLogService;
use Illuminate\Support\Facades\Auth;

class UserSanctionService
{
    protected UserSanctionRepository $repository;
    protected ModerationLogService $logService;

    public function __construct(UserSanctionRepository $repository, ModerationLogService $logService)
    {
        $this->repository = $repository;
        $this->logService = $logService;
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
}