<?php

namespace Modules\User\F27_GamificationLeaderboard\Services;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Cache;

class GamificationService
{
    public function getLeaderboard(int $limit = 10)
    {
        $page = request()->get('page', 1);
        $cacheKey = "leaderboard_{$limit}_page_{$page}";

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            return User::select('id', 'username', 'avatar_url', 'reputation_points', 'level')
                ->where('is_banned', false)
                ->orderBy('reputation_points', 'desc')
                ->orderBy('level', 'desc')
                ->paginate($limit);
        });
    }
}