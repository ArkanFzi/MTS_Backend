<?php

namespace Modules\User\F29_BadgeAchievement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gamification\Badge;

class BadgeAchievementController extends Controller
{
    /**
     * Return ALL badges, with earned_at populated for badges the user has earned.
     * Locked badges have earned_at = null.
     */
    public function index()
    {
        $user = auth()->user();

        // Get IDs of badges the user has earned
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        // Get ALL badges, attach earned_at only for earned ones
        $allBadges = Badge::orderBy('tier')->orderBy('condition_value')->get()->map(function ($badge) use ($user, $earnedBadgeIds) {
            $badgeData = $badge->toArray();

            if (in_array($badge->id, $earnedBadgeIds)) {
                $pivot = $user->badges()->where('badges.id', $badge->id)->first()?->pivot;
                $badgeData['earned_at'] = $pivot?->earned_at;
            } else {
                $badgeData['earned_at'] = null;
            }

            return $badgeData;
        });

        return response()->json([
            'success' => true,
            'data'    => $allBadges,
        ]);
    }
}
