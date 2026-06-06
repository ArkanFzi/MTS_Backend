<?php

namespace Modules\User\F30_UserReport\Repositories;

use App\Models\Moderation\Report;

class UserReportRepository
{
    public function create(array $data): Report
    {
        return Report::create($data);
    }
}