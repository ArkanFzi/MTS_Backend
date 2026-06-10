<?php

namespace Modules\User\F24_BookmarkPost\Repositories;

use App\Models\Interaction\Bookmark;
use Illuminate\Database\Eloquent\Collection;

class BookmarkRepository
{
    /**
     * Cek apakah user sudah mem-bookmark post tersebut.
     */
    public function isBookmarked(string $userId, string $postId): bool
    {
        return Bookmark::where('user_id', $userId)
            ->where('post_id', $postId)
            ->exists();
    }

    /**
     * Simpan bookmark baru ke database.
     */
    public function addBookmark(string $userId, string $postId): void
    {
        Bookmark::create([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }

    /**
     * Hapus bookmark dari database.
     */
    public function removeBookmark(string $userId, string $postId): void
    {
        Bookmark::where('user_id', $userId)
            ->where('post_id', $postId)
            ->delete();
    }

    /**
     * Ambil daftar bookmark milik user beserta data post-nya.
     */
    public function getBookmarkedPosts(string $userId): Collection
    {
        return Bookmark::where('user_id', $userId)
            ->with(['post.user:id,username,avatar_url', 'post.category:id,name,slug', 'post.tags:id,name,slug,color'])
            ->latest()
            ->get();
    }
}