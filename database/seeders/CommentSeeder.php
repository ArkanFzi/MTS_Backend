<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\Auth\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();
        $users = User::where('is_banned', false)->take(6)->get();

        if ($posts->isEmpty() || $users->count() < 3) {
            $this->command->warn('⚠️ CommentSeeder: Not enough posts or users. Skipping.');
            return;
        }

        $commentCount = 0;

        foreach ($posts as $postIndex => $post) {
            // Each post gets 2-4 root comments from different users (skip the post author)
            $commenters = $users->reject(fn($u) => $u->id === $post->user_id)->take(rand(2, 4));

            $rootComments = [];
            foreach ($commenters as $i => $commenter) {
                $comment = Comment::firstOrCreate(
                    [
                        'post_id' => $post->id,
                        'user_id' => $commenter->id,
                        'body' => $this->getCommentBody($postIndex, $i),
                    ],
                    [
                        'post_id' => $post->id,
                        'user_id' => $commenter->id,
                        'body' => $this->getCommentBody($postIndex, $i),
                        'parent_id' => null,
                        'vote_score' => rand(1, 15),
                        'is_accepted' => false,
                        'created_at' => $post->created_at->addHours(rand(1, 48)),
                    ]
                );
                $rootComments[] = $comment;
                $commentCount++;
            }

            // ─── Nested replies on some root comments ───
            if (count($rootComments) >= 2) {
                // Post author replies to the first comment
                $reply = Comment::firstOrCreate(
                    [
                        'post_id' => $post->id,
                        'user_id' => $post->user_id,
                        'parent_id' => $rootComments[0]->id,
                        'body' => 'Terima kasih atas masukannya, sangat membantu!',
                    ],
                    [
                        'post_id' => $post->id,
                        'user_id' => $post->user_id,
                        'parent_id' => $rootComments[0]->id,
                        'body' => 'Terima kasih atas masukannya, sangat membantu!',
                        'vote_score' => rand(1, 5),
                        'is_accepted' => false,
                        'created_at' => $rootComments[0]->created_at->addHours(rand(1, 12)),
                    ]
                );
                $commentCount++;

                // Another user replies to the second comment
                $reply2User = $users->reject(fn($u) =>
                    $u->id === $post->user_id || $u->id === $rootComments[1]->user_id
                )->first();

                if ($reply2User) {
                    Comment::firstOrCreate(
                        [
                            'post_id' => $post->id,
                            'user_id' => $reply2User->id,
                            'parent_id' => $rootComments[1]->id,
                            'body' => 'Saya setuju dengan pendapat ini, bisa jadi referensi.',
                        ],
                        [
                            'post_id' => $post->id,
                            'user_id' => $reply2User->id,
                            'parent_id' => $rootComments[1]->id,
                            'body' => 'Saya setuju dengan pendapat ini, bisa jadi referensi.',
                            'vote_score' => rand(0, 3),
                            'is_accepted' => false,
                            'created_at' => $rootComments[1]->created_at->addHours(rand(2, 24)),
                        ]
                    );
                    $commentCount++;
                }
            }

            // ─── Mark accepted answer on first 4 posts ───
            if ($postIndex < 4 && count($rootComments) >= 2) {
                $accepted = $rootComments[1]; // Second comment = accepted answer
                $accepted->update([
                    'is_accepted' => true,
                ]);

                // Also update the post's is_answered flag
                $post->update([
                    'is_answered' => true,
                    'accepted_answer_id' => $accepted->id,
                ]);
            }
        }

        $this->command->info("✅ {$commentCount} Comments created (with nested replies + accepted answers).");
    }

    private function getCommentBody(int $postIdx, int $commentIdx): string
    {
        $bodies = [
            // Generic helpful comments
            'Penjelasan yang sangat jelas, terima kasih!',
            'Saya sudah coba dan berhasil, mantap!',
            'Mungkin bisa ditambahkan contoh kodenya juga.',
            'Bermanfaat banget buat pemula seperti saya.',
            'Setuju! Ini memang cara yang paling efektif.',
            'Ada alternatif lain juga yang bisa dicoba.',
            'Keren, ini yang lagi saya cari.',
            'Bisa share repo atau referensinya?',
            'Nice tutorial! Lanjutkan seriesnya dong.',
            'Saya punya pengalaman serupa, hasilnya memuaskan.',
            'Bagaimana kalau diterapkan di production?',
            'Wah, baru tahu ada cara seperti ini.',
        ];

        return $bodies[($postIdx + $commentIdx) % count($bodies)];
    }
}
