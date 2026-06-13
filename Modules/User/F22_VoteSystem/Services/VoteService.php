<?php

namespace Modules\User\F22_VoteSystem\Services;

use Modules\User\F22_VoteSystem\Repositories\VoteRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Auth\User;

class VoteService
{
    public function __construct(
        protected VoteRepository $repository,
        protected NotificationService $notificationService,
        protected BadgeAchievementService $gamification
    ) {}

    public function vote(string $userId, string $targetId, string $targetType, string $voteInput): array
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        if ($model && $model->user_id === $userId) {
            abort(403, 'Anda tidak bisa memberikan vote pada konten Anda sendiri.');
        }

        $typeValue = ($voteInput === 'up') ? 1 : -1;
        $existingVote = $this->repository->getExistingVote($userId, $targetId, $targetType);

        if (!$existingVote) {
            $this->repository->createVote($userId, $targetId, $targetType, $typeValue);
            $action = 'voted';
            $this->handlePoints($targetId, $targetType, $typeValue, $userId);
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);

        } elseif ((int) $existingVote->vote_type === $typeValue) {
            // Cancel vote → kembalikan poin
            $this->repository->deleteVote($existingVote);
            $action = 'canceled';
            $this->handlePoints($targetId, $targetType, -$typeValue, $userId); // balik poin

        } else {
            // Ganti vote (up→down atau down→up)
            $this->repository->updateVote($existingVote, $typeValue);
            $action = 'updated';
            // Kurangi poin lama, tambah poin baru
            $this->handlePoints($targetId, $targetType, -(int)$existingVote->vote_type, $userId);
            $this->handlePoints($targetId, $targetType, $typeValue, $userId);
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);
        }

        $actionScore = $this->repository->getScore($targetId, $targetType);
        $this->syncVoteScore($targetId, $targetType, $actionScore);

        return ['action' => $action, 'score' => $actionScore];
    }

    protected function handlePoints(string $targetId, string $targetType, int $typeValue, string $actorId): void
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        if (!$model) return;

        // Jangan kasih poin jika vote konten sendiri
        if ($model->user_id === $actorId) return;

        $owner = User::find($model->user_id);
        if (!$owner) return;

        if ($targetType === 'post') {
            $points = $typeValue === 1 ? 5 : -2;
            $action = $typeValue === 1 ? 'upvote_received_post' : 'downvote_received_post';
        } else {
            $points = $typeValue === 1 ? 3 : -2;
            $action = $typeValue === 1 ? 'upvote_received_comment' : 'downvote_received_comment';
        }

        $this->gamification->addPoints($owner, $points, $action, $targetId);
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