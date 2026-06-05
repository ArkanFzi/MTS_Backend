<?php

namespace Modules\User\F22_VoteSystem\Services;

use Modules\User\F22_VoteSystem\Repositories\VoteRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use App\Models\Content\Post;
use App\Models\Content\Comment;

class VoteService
{
    public function __construct(
        protected VoteRepository $repository,
        protected NotificationService $notificationService
    ) {}

    public function vote(string $userId, string $targetId, string $targetType, string $voteInput): array
    {
        $typeValue = ($voteInput === 'up') ? 1 : -1;
        $existingVote = $this->repository->getExistingVote($userId, $targetId, $targetType);

        if (!$existingVote) {
            $this->repository->createVote($userId, $targetId, $targetType, $typeValue);
            $action = 'voted';
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);
        } elseif ($existingVote->vote_type === $typeValue) {
            $this->repository->deleteVote($existingVote);
            $action = 'canceled';
        } else {
            $this->repository->updateVote($existingVote, $typeValue);
            $action = 'updated';
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);
        }

        $actionScore = $this->repository->getScore($targetId, $targetType);
        $this->syncVoteScore($targetId, $targetType, $actionScore);

        return [
            'action' => $action,
            'score'  => $actionScore
        ];
    }

    protected function syncVoteScore(string $targetId, string $targetType, int $score): void
    {
        if ($targetType === 'post') {
            Post::where('id', $targetId)->update(['vote_score' => $score]);
        } elseif ($targetType === 'comment') {
            Comment::where('id', $targetId)->update(['vote_score' => $score]);
        }
    }

    protected function sendNotification(string $actorId, string $targetId, string $targetType): void
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        if ($model && $model->user_id !== $actorId) {
            $this->notificationService->createNotification(
                $model->user_id, $actorId, "upvote_{$targetType}", $targetId, $targetType
            );
        }
    }
}