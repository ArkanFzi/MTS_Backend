<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gamification\PointsLog;
use App\Models\Gamification\UserBadge;
use App\Models\Gamification\Badge;
use App\Models\Auth\User;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_banned', false)->get();
        $badges = Badge::all();

        if ($users->isEmpty() || $badges->isEmpty()) {
            $this->command->warn('⚠️ GamificationSeeder: No users or badges found. Skipping.');
            return;
        }

        $pointsCount = 0;
        $badgeCount = 0;

        // ─── 1. Points Log (recent entries for admin dashboard 7-day chart) ───
        $pointActions = [
            ['type' => 'post_created', 'points' => 10, 'desc' => 'Created a new post'],
            ['type' => 'comment_created', 'points' => 5, 'desc' => 'Commented on a post'],
            ['type' => 'answer_accepted', 'points' => 15, 'desc' => 'Answer marked as accepted'],
            ['type' => 'upvote_received', 'points' => 3, 'desc' => 'Received an upvote'],
            ['type' => 'downvote_received', 'points' => -1, 'desc' => 'Received a downvote'],
            ['type' => 'badge_earned', 'points' => 25, 'desc' => 'Earned a badge'],
        ];

        foreach ($users as $user) {
            // Each user gets 5-15 point log entries spread across the last 7 days
            $entryCount = rand(5, 15);

            for ($i = 0; $i < $entryCount; $i++) {
                $action = $pointActions[array_rand($pointActions)];

                PointsLog::create([
                    'user_id' => $user->id,
                    'points' => $action['points'],
                    'action_type' => $action['type'],
                    'reference_id' => fake()->uuid(),
                    'description' => $action['desc'],
                    'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                ]);
                $pointsCount++;
            }
        }

        // ─── 2. User Badges ───
        $badgeAssignments = [
            // [username, badge_condition_type, min_condition_value_match]
            // Post count badges: Newcomer(1), Active Member(10), Veteran(50)
            ['userbiasa', 'post_count', 1],
            ['moderator_suhu', 'post_count', 1],
            ['budi_dev', 'post_count', 1],
            // Reputation badges: Rising Star(100), Elite(500), Legend(1000)
            ['userbiasa', 'reputation_points', 100],      // 25 points - no match, skip
            ['moderator_suhu', 'reputation_points', 100],  // 250 pts -> Rising Star
            ['dewi_react', 'reputation_points', 100],      // 350 pts -> Rising Star
            ['admin_master', 'reputation_points', 500],    // 1000 pts -> Legend
            // Answer accepted badges: Helper(1), Expert(10)
            ['budi_dev', 'answer_accepted', 1],
            ['moderator_suhu', 'answer_accepted', 1],
            ['siti_coder', 'answer_accepted', 1],
            // Upvote received badges: Popular(10), Influential(50)
            ['userbiasa', 'upvote_received', 10],
            ['dewi_react', 'upvote_received', 10],
            ['budi_dev', 'upvote_received', 10],
        ];

        foreach ($badgeAssignments as [$username, $condType, $condVal]) {
            $user = $users->firstWhere('username', $username);
            if (!$user) continue;

            // Find a matching badge by condition_type and condition_value <= $condVal
            $badge = $badges->where('condition_type', $condType)
                ->where('condition_value', '<=', $condVal)
                ->sortByDesc('condition_value')
                ->first();

            if (!$badge) continue;

            // Avoid duplicate
            $exists = UserBadge::where('user_id', $user->id)
                ->where('badge_id', $badge->id)
                ->exists();

            if (!$exists) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'earned_at' => now()->subDays(rand(1, 30)),
                ]);
                $badgeCount++;
            }
        }

        $this->command->info("✅ Gamification data created:");
        $this->command->info("   • {$pointsCount} points log entries (spread across last 7 days)");
        $this->command->info("   • {$badgeCount} user-badge assignments");
    }
}
