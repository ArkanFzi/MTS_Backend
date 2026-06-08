<?php

namespace Modules\User\F29_BadgeAchievement\Services;

use App\Models\Auth\User;
use App\Models\Gamification\PointsLog;
use App\Models\Gamification\Badge;
use App\Models\Interaction\Vote;
use Illuminate\Support\Facades\DB;
use Modules\User\F26_NotificationSystem\Services\NotificationService;

class BadgeAchievementService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function addPoints(User $user, int $points, string $actionType, ?string $refId = null, ?string $description = null)
    {
        return DB::transaction(function () use ($user, $points, $actionType, $refId, $description) {
            PointsLog::create([
                'user_id'      => $user->id,
                'points'       => $points,
                'action_type'  => $actionType,
                'reference_id' => $refId,
                'description'  => $description,
                'created_at'   => now(),
            ]);

            $user->increment('reputation_points', $points);

            $newLevel = floor($user->reputation_points / 50) + 1;
            if ($newLevel > $user->level) {
                $user->update(['level' => $newLevel]);
            }

            $this->checkAndAwardBadges($user);

            return $user->fresh();
        });
    }

    public function checkAndAwardBadges(User $user)
    {
        $user->loadCount([
            'posts',
            'comments',
            'comments as answer_accepted_count' => fn($q) => $q->where('is_accepted', true),
        ]);

        // Hitung upvote yang diterima dari post DAN comment milik user
        $postIds    = $user->posts()->pluck('id');
        $commentIds = $user->comments()->pluck('id');

        $upvoteReceived = Vote::where('vote_type', 1)
            ->where(function ($q) use ($postIds, $commentIds) {
                $q->whereIn('target_id', $postIds)
                  ->orWhereIn('target_id', $commentIds);
            })->count();

        $availableBadges = Badge::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        foreach ($availableBadges as $badge) {
            $awarded = false;

            switch ($badge->condition_type) {
                case 'reputation_points':
                    if ($user->reputation_points >= $badge->condition_value) $awarded = true;
                    break;
                case 'post_count':
                    if ($user->posts_count >= $badge->condition_value) $awarded = true;
                    break;
                case 'comment_count':
                    if ($user->comments_count >= $badge->condition_value) $awarded = true;
                    break;
                case 'answer_accepted':
                    if ($user->answer_accepted_count >= $badge->condition_value) $awarded = true;
                    break;
                case 'upvote_received':
                    if ($upvoteReceived >= $badge->condition_value) $awarded = true;
                    break;
            }

            if ($awarded) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);

                $this->notificationService->createNotification(
                    $user->id,
                    $user->id,
                    'badge_awarded',
                    $badge->id,
                    'badge'
                );
            }
        }
    }
}