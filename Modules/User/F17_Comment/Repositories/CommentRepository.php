<?php

namespace Modules\User\F17_Comment\Repositories;

use App\Models\Content\Comment;

class CommentRepository
{
    public function create(array $data)
    {
        return Comment::create($data);
    }

    public function getByPost(string $postId)
    {
        // Mengambil komentar root (tanpa parent) beserta replies-nya
        return Comment::where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();
    }

    public function findById(string $id)
    {
        return Comment::findOrFail($id);
    }

    public function delete(string $id)
    {
        return Comment::findOrFail($id)->delete();
    }
}
