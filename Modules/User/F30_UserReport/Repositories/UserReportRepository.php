<?php

namespace Modules\User\F30_UserReport\Repositories;

use App\Models\Moderation\Report;

class UserReportRepository
{
    public function create(array $data): Report
    {
        return Report::create($data);
    }

    public function findExisting(string $reporterId, string $targetId, string $targetType): ?Report
    {
        return Report::where('reporter_id', $reporterId)
            ->where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->first();
    }
}