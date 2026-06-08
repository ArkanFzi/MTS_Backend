<?php

namespace Modules\User\F29_BadgeAchievement\Services;

use App\Models\Auth\User;
use App\Models\Gamification\PointsLog;
use App\Models\Gamification\Badge;
use Illuminate\Support\Facades\DB;
use Modules\User\F26_NotificationSystem\Services\NotificationService;

class BadgeAchievementService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tambah poin ke user dan catat log-nya + Cek Badge.
     */
    public function addPoints(User $user, int $points, string $actionType, ?string $refId = null, ?string $description = null)
    {
        return DB::transaction(function () use ($user, $points, $actionType, $refId, $description) {
            // 1. Catat Log
            PointsLog::create([
                'user_id'     => $user->id,
                'points'      => $points,
                'action_type' => $actionType,
                'reference_id' => $refId,
                'description' => $description,
                'created_at'  => now(),
            ]);

            // 2. Update Reputasi User
            $user->increment('reputation_points', $points);

            // 3. Cek kenaikan level
            $newLevel = floor($user->reputation_points / 50) + 1;
            if ($newLevel > $user->level) {
                $user->update(['level' => $newLevel]);
            }

            // 4. Cek Badge
            $this->checkAndAwardBadges($user);

            return $user->fresh();
        });
    }

    /**
     * Logic otomatis pemberian badge berdasarkan aktivitas user.
     */
    public function checkAndAwardBadges(User $user)
    {
        $user->loadCount(['posts', 'comments']);
        
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
            }

            if ($awarded) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);

                // Kirim Notifikasi
                $this->notificationService->createNotification(
                    $user->id,
                    $user->id, 
                    'badge_awarded',
                    $badge->id,
                    Badge::class
                );
            }
        }
    }
}
