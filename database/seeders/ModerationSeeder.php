<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Moderation\Report;
use App\Models\Moderation\ModerationLog;
use App\Models\Moderation\Notification;
use App\Models\History\PostEditHistory;
use App\Models\History\CommentEditHistory;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Auth\User;

class ModerationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $moderator = $users->firstWhere('username', 'moderator_suhu');
        $admin = $users->firstWhere('username', 'admin_master');
        $regularUsers = $users->where('is_banned', false)->filter(fn($u) => $u->username !== 'moderator_suhu' && $u->username !== 'admin_master');
        $posts = Post::all();
        $comments = Comment::whereNull('parent_id')->get();

        if (!$moderator || !$admin || $posts->isEmpty()) {
            $this->command->warn('⚠️ ModerationSeeder: Missing moderator/admin or posts. Skipping.');
            return;
        }

        $reportCount = 0;
        $logCount = 0;
        $notifCount = 0;
        $editCount = 0;

        // ─── 1. Reports (mix of pending + resolved) ───
        $reportData = [
            // Pending reports
            ['reporter' => 'budi_dev', 'target_type' => 'post', 'target_idx' => 2, 'reason' => 'Spam', 'desc' => 'Post ini sepertinya spam, mengandung link mencurigakan.', 'status' => 'pending'],
            ['reporter' => 'siti_coder', 'target_type' => 'post', 'target_idx' => 5, 'reason' => 'Duplicate', 'desc' => 'Pertanyaan ini sudah pernah ditanyakan sebelumnya.', 'status' => 'pending'],
            ['reporter' => 'andi_js', 'target_type' => 'comment', 'target_idx' => 0, 'reason' => 'Inappropriate', 'desc' => 'Komentar mengandung bahasa kasar.', 'status' => 'pending'],
            ['reporter' => 'rizki_php', 'target_type' => 'post', 'target_idx' => 7, 'reason' => 'Off-topic', 'desc' => 'Post ini tidak sesuai dengan kategori yang dipilih.', 'status' => 'pending'],
            // Resolved reports
            ['reporter' => 'dewi_react', 'target_type' => 'post', 'target_idx' => 8, 'reason' => 'Spam', 'desc' => 'Post berisi promosi yang tidak relevan.', 'status' => 'resolved', 'resolver' => 'moderator_suhu', 'days_ago' => 3],
            ['reporter' => 'putri_vue', 'target_type' => 'comment', 'target_idx' => 3, 'reason' => 'Harassment', 'desc' => 'Komentar ini menyerang pengguna lain.', 'status' => 'resolved', 'resolver' => 'moderator_suhu', 'days_ago' => 5],
            ['reporter' => 'userbiasa', 'target_type' => 'post', 'target_idx' => 9, 'reason' => 'Low quality', 'desc' => 'Post terlalu singkat dan tidak jelas.', 'status' => 'resolved', 'resolver' => 'admin_master', 'days_ago' => 7],
        ];

        foreach ($reportData as $data) {
            $reporter = $users->firstWhere('username', $data['reporter']);
            if (!$reporter) continue;

            $target = $data['target_type'] === 'post'
                ? ($posts[$data['target_idx']] ?? null)
                : ($comments[$data['target_idx']] ?? null);
            if (!$target) continue;

            $resolver = isset($data['resolver']) ? $users->firstWhere('username', $data['resolver']) : null;

            Report::firstOrCreate(
                [
                    'reporter_id' => $reporter->id,
                    'target_id' => $target->id,
                    'target_type' => $data['target_type'],
                ],
                [
                    'reporter_id' => $reporter->id,
                    'target_id' => $target->id,
                    'target_type' => $data['target_type'],
                    'reason' => $data['reason'],
                    'description' => $data['desc'],
                    'status' => $data['status'],
                    'resolved_by' => $resolver?->id,
                    'created_at' => now()->subDays($data['days_ago'] ?? rand(1, 3)),
                    'resolved_at' => $resolver ? now()->subDays(($data['days_ago'] ?? 1) - 1) : null,
                ]
            );
            $reportCount++;
        }

        // ─── 2. Moderation Logs (admin/moderator actions) ───
        $bannedUser = $users->firstWhere('username', 'user_nakal');
        $targetUsers = $regularUsers->take(4);

        $modActions = [
            ['mod' => $moderator, 'target' => $bannedUser, 'action' => 'ban', 'reason' => 'Repeated spam posts', 'notes' => 'User repeatedly posting spam content. Permanent ban issued.', 'days_ago' => 10],
            ['mod' => $moderator, 'target' => $targetUsers->first(), 'action' => 'warn', 'reason' => 'Inappropriate language', 'notes' => 'Warning issued for using inappropriate language in comments.', 'days_ago' => 7],
            ['mod' => $admin, 'target' => $bannedUser, 'action' => 'ban', 'reason' => 'Harassment', 'notes' => 'Banned for harassing other users in multiple threads.', 'days_ago' => 12],
            ['mod' => $admin, 'target' => $targetUsers->skip(1)->first(), 'action' => 'warn', 'reason' => 'Off-topic posting', 'notes' => 'User posted off-topic content in multiple categories.', 'days_ago' => 5],
            ['mod' => $moderator, 'target' => $targetUsers->skip(2)->first(), 'action' => 'warn', 'reason' => 'Self-promotion', 'notes' => 'Excessive self-promotion in answers.', 'days_ago' => 3],
            ['mod' => $admin, 'target' => $targetUsers->skip(3)->first(), 'action' => 'mute', 'reason' => 'Flooding', 'notes' => 'Temporarily muted for flooding the forum.', 'days_ago' => 1],
        ];

        foreach ($modActions as $data) {
            if (!$data['mod'] || !$data['target']) continue;

            ModerationLog::firstOrCreate(
                [
                    'moderator_id' => $data['mod']->id,
                    'target_user_id' => $data['target']->id,
                    'action_type' => $data['action'],
                ],
                [
                    'moderator_id' => $data['mod']->id,
                    'target_user_id' => $data['target']->id,
                    'action_type' => $data['action'],
                    'reason' => $data['reason'],
                    'notes' => $data['notes'],
                    'created_at' => now()->subDays($data['days_ago']),
                ]
            );
            $logCount++;
        }

        // ─── 3. Notifications (various types) ───
        $notificationData = [
            // Comment notifications
            ['user' => 'userbiasa', 'actor' => 'budi_dev', 'type' => 'comment', 'ref_type' => 'post', 'ref_idx' => 0, 'read' => true, 'days_ago' => 2],
            ['user' => 'userbiasa', 'actor' => 'siti_coder', 'type' => 'comment', 'ref_type' => 'post', 'ref_idx' => 0, 'read' => true, 'days_ago' => 2],
            ['user' => 'userbiasa', 'actor' => 'dewi_react', 'type' => 'comment', 'ref_type' => 'post', 'ref_idx' => 1, 'read' => false, 'days_ago' => 1],
            ['user' => 'moderator_suhu', 'actor' => 'andi_js', 'type' => 'comment', 'ref_type' => 'post', 'ref_idx' => 2, 'read' => false, 'days_ago' => 1],
            ['user' => 'moderator_suhu', 'actor' => 'rizki_php', 'type' => 'comment', 'ref_type' => 'post', 'ref_idx' => 3, 'read' => true, 'days_ago' => 4],
            // Upvote notifications
            ['user' => 'userbiasa', 'actor' => 'putri_vue', 'type' => 'upvote', 'ref_type' => 'post', 'ref_idx' => 0, 'read' => true, 'days_ago' => 3],
            ['user' => 'budi_dev', 'actor' => 'userbiasa', 'type' => 'upvote', 'ref_type' => 'post', 'ref_idx' => 4, 'read' => false, 'days_ago' => 1],
            ['user' => 'siti_coder', 'actor' => 'dewi_react', 'type' => 'upvote', 'ref_type' => 'post', 'ref_idx' => 6, 'read' => false, 'days_ago' => 0],
            // Accepted answer notifications
            ['user' => 'budi_dev', 'actor' => 'userbiasa', 'type' => 'accepted_answer', 'ref_type' => 'post', 'ref_idx' => 0, 'read' => true, 'days_ago' => 2],
            ['user' => 'siti_coder', 'actor' => 'moderator_suhu', 'type' => 'accepted_answer', 'ref_type' => 'post', 'ref_idx' => 2, 'read' => false, 'days_ago' => 1],
            // Follow notifications
            ['user' => 'moderator_suhu', 'actor' => 'userbiasa', 'type' => 'follow', 'ref_type' => 'user', 'ref_idx' => 0, 'read' => true, 'days_ago' => 5],
            ['user' => 'dewi_react', 'actor' => 'budi_dev', 'type' => 'follow', 'ref_type' => 'user', 'ref_idx' => 0, 'read' => true, 'days_ago' => 4],
            ['user' => 'dewi_react', 'actor' => 'rizki_php', 'type' => 'follow', 'ref_type' => 'user', 'ref_idx' => 0, 'read' => false, 'days_ago' => 1],
            // Badge earned notification
            ['user' => 'userbiasa', 'actor' => 'admin_master', 'type' => 'badge', 'ref_type' => 'user', 'ref_idx' => 0, 'read' => false, 'days_ago' => 0],
        ];

        foreach ($notificationData as $data) {
            $user = $users->firstWhere('username', $data['user']);
            $actor = $users->firstWhere('username', $data['actor']);
            if (!$user || !$actor) continue;

            $refTarget = null;
            if ($data['ref_type'] === 'post') {
                $refTarget = $posts[$data['ref_idx']] ?? null;
            } elseif ($data['ref_type'] === 'user') {
                $refTarget = $user;
            }

            Notification::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'actor_id' => $actor->id,
                    'type' => $data['type'],
                    'reference_id' => $refTarget?->id ?? $user->id,
                    'reference_type' => $data['ref_type'] === 'post' ? 'post' : 'user',
                ],
                [
                    'user_id' => $user->id,
                    'actor_id' => $actor->id,
                    'type' => $data['type'],
                    'reference_id' => $refTarget?->id ?? $user->id,
                    'reference_type' => $data['ref_type'] === 'post' ? 'post' : 'user',
                    'is_read' => $data['read'],
                    'created_at' => now()->subDays($data['days_ago']),
                ]
            );
            $notifCount++;
        }

        // ─── 4. Post Edit Histories ───
        $postEditors = [$moderator, $admin, $users->firstWhere('username', 'budi_dev')];
        foreach ($posts->take(6) as $i => $post) {
            $editor = $postEditors[$i % count($postEditors)];
            if (!$editor) continue;

            PostEditHistory::firstOrCreate(
                ['post_id' => $post->id, 'edited_by' => $editor->id],
                [
                    'post_id' => $post->id,
                    'edited_by' => $editor->id,
                    'body_before' => 'Original body content before edit...',
                    'body_after' => $post->body,
                    'reason' => ['Perbaikan format', 'Menambahkan detail', 'Memperbaiki typo', 'Update informasi', 'Perbaikan kode', 'Klarifikasi pertanyaan'][$i % 6],
                    'edited_at' => $post->created_at->addHours(rand(2, 72)),
                ]
            );
            $editCount++;
        }

        // ─── 5. Comment Edit Histories ───
        $commentEditors = [$moderator, $users->firstWhere('username', 'siti_coder'), $users->firstWhere('username', 'dewi_react')];
        foreach ($comments->take(5) as $i => $comment) {
            $editor = $commentEditors[$i % count($commentEditors)];
            if (!$editor) continue;

            CommentEditHistory::firstOrCreate(
                ['comment_id' => $comment->id, 'edited_by' => $editor->id],
                [
                    'comment_id' => $comment->id,
                    'edited_by' => $editor->id,
                    'body_before' => 'Original comment before edit...',
                    'body_after' => $comment->body,
                    'edited_at' => $comment->created_at->addHours(rand(1, 24)),
                ]
            );
            $editCount++;
        }

        $this->command->info("✅ Moderation data created:");
        $this->command->info("   • {$reportCount} reports (4 pending + 3 resolved)");
        $this->command->info("   • {$logCount} moderation logs (ban, warn, mute)");
        $this->command->info("   • {$notifCount} notifications (comments, votes, follows, badges)");
        $this->command->info("   • {$editCount} edit histories (posts + comments)");
    }
}
