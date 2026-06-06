<?php

namespace Modules\User\F29_BadgeAchievement\Repositories;

use App\Models\Gamification\Badge;
use App\Models\Auth\User;

class BadgeAchievementRepository
{
    public function getUnearnedBadges(User $user)
    {
        return Badge::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();
    }

    public function awardBadgeToUser(User $user, string $badgeId)
    {
        return $user->badges()->attach($badgeId, ['earned_at' => now()]);
    }
}
