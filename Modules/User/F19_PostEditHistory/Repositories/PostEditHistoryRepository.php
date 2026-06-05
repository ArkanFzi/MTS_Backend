<?php

namespace Modules\User\F19_PostEditHistory\Repositories;

use App\Models\History\PostEditHistory;
use Illuminate\Database\Eloquent\Collection;

class PostEditHistoryRepository
{
    public function getHistoryByPost(string $postId): Collection
    {
        return PostEditHistory::where('post_id', $postId)
            ->with('editor:id,name') // Memuat relasi user yang mengedit
            ->orderBy('edited_at', 'desc')
            ->get();
    }
}