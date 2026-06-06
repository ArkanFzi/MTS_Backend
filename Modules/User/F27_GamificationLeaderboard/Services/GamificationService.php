<?php

namespace Modules\User\F27_GamificationLeaderboard\Services;

use App\Models\Auth\User;

class GamificationService
{
    public function getLeaderboard(int $limit = 10)
    {
        return User::select('id', 'username', 'avatar_url', 'reputation_points', 'level')
            ->where('is_banned', false)
            ->orderBy('reputation_points', 'desc')
            ->orderBy('level', 'desc')
            ->paginate($limit);
    }
}
