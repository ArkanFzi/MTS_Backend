<?php

namespace Modules\Moderator\F14_ModeratorActionLog\Services;

use Modules\Moderator\F14_ModeratorActionLog\Repositories\ModerationLogRepository;

class ModerationLogService
{
    protected ModerationLogRepository $repository;

    public function __construct(ModerationLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPaginated(int $perPage = 20, $actionType = null, $search = null)
    {
        return $this->repository->getAllPaginated($perPage, $actionType, $search);
    }

    public function logAction(array $data)
    {
        return $this->repository->create($data);
    }
}