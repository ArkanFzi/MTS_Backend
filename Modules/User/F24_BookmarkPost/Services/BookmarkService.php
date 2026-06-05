<?php

namespace Modules\User\F24_BookmarkPost\Services;

use Modules\User\F24_BookmarkPost\Repositories\BookmarkRepository;

class BookmarkService
{
    public function __construct(
        protected BookmarkRepository $repository
    ) {}

    public function toggleBookmark(string $userId, string $postId): string
    {
        if ($this->repository->isBookmarked($userId, $postId)) {
            $this->repository->removeBookmark($userId, $postId);
            return 'unbookmarked';
        }

        $this->repository->addBookmark($userId, $postId);
        return 'bookmarked';
    }

    public function getMyBookmarks(string $userId)
    {
        return $this->repository->getBookmarkedPosts($userId);
    }
}