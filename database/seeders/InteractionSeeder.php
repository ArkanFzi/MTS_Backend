<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interaction\Vote;
use App\Models\Interaction\Like;
use App\Models\Interaction\Bookmark;
use App\Models\Interaction\Follow;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Auth\User;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_banned', false)->get();
        $posts = Post::all();
        $comments = Comment::whereNull('parent_id')->get(); // Only root comments

        if ($users->count() < 3 || $posts->isEmpty()) {
            $this->command->warn('⚠️ InteractionSeeder: Not enough data. Skipping.');
            return;
        }

        $voteCount = 0;
        $likeCount = 0;
        $bookmarkCount = 0;
        $followCount = 0;

        // ─── 1. Votes on Posts (upvote/downvote) ───
        foreach ($posts as $post) {
            // 3-6 random users vote on each post
            $voters = $users->reject(fn($u) => $u->id === $post->user_id)
                ->shuffle()
                ->take(rand(3, 6));

            foreach ($voters as $voter) {
                Vote::firstOrCreate(
                    ['user_id' => $voter->id, 'target_id' => $post->id, 'target_type' => 'post'],
                    ['vote_type' => rand(0, 10) > 2 ? 'upvote' : 'downvote']
                );
                $voteCount++;
            }
        }

        // ─── 2. Votes on Comments ───
        foreach ($comments as $comment) {
            $voters = $users->reject(fn($u) => $u->id === $comment->user_id)
                ->shuffle()
                ->take(rand(1, 4));

            foreach ($voters as $voter) {
                Vote::firstOrCreate(
                    ['user_id' => $voter->id, 'target_id' => $comment->id, 'target_type' => 'comment'],
                    ['vote_type' => rand(0, 10) > 3 ? 'upvote' : 'downvote']
                );
                $voteCount++;
            }
        }

        // ─── 3. Likes on Posts ───
        foreach ($posts as $post) {
            $likers = $users->reject(fn($u) => $u->id === $post->user_id)
                ->shuffle()
                ->take(rand(2, 5));

            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'user_id' => $liker->id,
                    'target_id' => $post->id,
                    'target_type' => 'post',
                ]);
                $likeCount++;
            }
        }

        // ─── 4. Likes on Comments ───
        foreach ($comments->take(8) as $comment) {
            $likers = $users->reject(fn($u) => $u->id === $comment->user_id)
                ->shuffle()
                ->take(rand(1, 3));

            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'user_id' => $liker->id,
                    'target_id' => $comment->id,
                    'target_type' => 'comment',
                ]);
                $likeCount++;
            }
        }

        // ─── 5. Bookmarks ───
        foreach ($users->take(6) as $user) {
            $toBookmark = $posts->shuffle()->take(rand(2, 4));

            foreach ($toBookmark as $post) {
                Bookmark::firstOrCreate([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
                $bookmarkCount++;
            }
        }

        // ─── 6. Follows (who follows who) ───
        $followPairs = [
            ['userbiasa', 'moderator_suhu'],
            ['userbiasa', 'dewi_react'],
            ['userbiasa', 'siti_coder'],
            ['budi_dev', 'moderator_suhu'],
            ['budi_dev', 'dewi_react'],
            ['siti_coder', 'admin_master'],
            ['siti_coder', 'dewi_react'],
            ['andi_js', 'budi_dev'],
            ['andi_js', 'putri_vue'],
            ['rizki_php', 'moderator_suhu'],
            ['rizki_php', 'siti_coder'],
            ['rizki_php', 'dewi_react'],
            ['putri_vue', 'admin_master'],
            ['putri_vue', 'dewi_react'],
            ['dewi_react', 'admin_master'],
        ];

        foreach ($followPairs as [$followerName, $followingName]) {
            $follower = $users->firstWhere('username', $followerName);
            $following = $users->firstWhere('username', $followingName);

            if ($follower && $following && $follower->id !== $following->id) {
                Follow::firstOrCreate([
                    'follower_id' => $follower->id,
                    'following_id' => $following->id,
                ]);
                $followCount++;
            }
        }

        $this->command->info("✅ Interactions created:");
        $this->command->info("   • {$voteCount} votes (posts + comments)");
        $this->command->info("   • {$likeCount} likes (posts + comments)");
        $this->command->info("   • {$bookmarkCount} bookmarks");
        $this->command->info("   • {$followCount} follows");
    }
}
