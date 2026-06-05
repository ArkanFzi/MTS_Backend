<?php

namespace Modules\User\F21_CommentEditHistory\Services;

use Modules\User\F21_CommentEditHistory\Repositories\CommentEditHistoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CommentHistoryService
{
    public function __construct(
        protected CommentEditHistoryRepository $repository
    ) {}

    public function getHistory(string $commentId): Collection
    {
        return $this->repository->getHistoryByComment($commentId);
    }
}