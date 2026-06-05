<?php

namespace Modules\Admin\F14_ModeratorActionLog\Repositories;

use App\Models\Moderation\ModerationLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModerationLogRepository
{
    protected ModerationLog $model;

    public function __construct(ModerationLog $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $perPage = 20, $actionType = null, $search = null): LengthAwarePaginator
    {
        return $this->model
            ->with(['moderator', 'targetUser'])
            ->when($actionType, fn($q) => $q->where('action_type', $actionType))
            ->when($search, function ($q, $search) {
                $q->whereHas('targetUser', fn($tq) => $tq->where('username', 'like', "%{$search}%"))
                  ->orWhereHas('moderator', fn($mq) => $mq->where('username', 'like', "%{$search}%"))
                  ->orWhere('reason', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): ModerationLog
    {
        $data['created_at'] = now();
        return $this->model->create($data);
    }
}