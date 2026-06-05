<?php

namespace Modules\User\F21_CommentEditHistory\Repositories;

use App\Models\History\CommentEditHistory;
use Illuminate\Database\Eloquent\Collection;

class CommentEditHistoryRepository
{
    public function getHistoryByComment(string $commentId): Collection
    {
        return CommentEditHistory::where('comment_id', $commentId)
            ->with('editor:id,username')
            ->orderBy('edited_at', 'desc')
            ->get();
    }
}