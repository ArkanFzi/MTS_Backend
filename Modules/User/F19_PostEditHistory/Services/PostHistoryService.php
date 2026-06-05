<?php

namespace Modules\User\F19_PostEditHistory\Services;

use Modules\User\F19_PostEditHistory\Repositories\PostEditHistoryRepository;
use Illuminate\Database\Eloquent\Collection;

class PostHistoryService
{
    public function __construct(
        protected PostEditHistoryRepository $repository
    ) {}

    public function getHistory(string $postId): Collection
    {
        return $this->repository->getHistoryByPost($postId);
    }
}