<?php

namespace Modules\User\F27_GamificationLeaderboard\Services;

use App\Models\Auth\User;
use App\Models\Gamification\PointsLog;
use App\Models\Gamification\Badge;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Tambah poin ke user dan catat log-nya.
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

            // 3. Cek kenaikan level (misal tiap 100 poin naik level)
            $newLevel = floor($user->reputation_points / 100) + 1;
            if ($newLevel > $user->level) {
                $user->update(['level' => $newLevel]);
            }

            // 4. Cek Badge Awarding
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
        
        // Ambil semua badge yang belum dimiliki user
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
            }
        }
    }

    /**
     * Ambil daftar user dengan reputasi tertinggi (Leaderboard).
     */
    public function getLeaderboard(int $limit = 10)
    {
        return User::select('id', 'username', 'avatar_url', 'reputation_points', 'level')
            ->where('is_banned', false)
            ->orderBy('reputation_points', 'desc')
            ->orderBy('level', 'desc')
            ->paginate($limit);
    }
}
