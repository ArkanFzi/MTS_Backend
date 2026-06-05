<?php

namespace Modules\User\F18_MarkAcceptedAnswer\Repositories;

use App\Models\Content\Post;
use App\Models\Content\Comment;
use Illuminate\Support\Facades\DB;

class AcceptedAnswerRepository
{
    public function markAccepted(string $postId, string $commentId): void
    {
        DB::transaction(function () use ($postId, $commentId) {
            // Reset status accepted sebelumnya jika ada
            Comment::where('post_id', $postId)->update(['is_accepted' => false]);

            // Set accepted untuk comment terpilih
            Comment::where('id', $commentId)->update(['is_accepted' => true]);

            // Update Post
            Post::where('id', $postId)->update([
                'is_answered' => true,
                'accepted_answer_id' => $commentId
            ]);
        });
    }
}