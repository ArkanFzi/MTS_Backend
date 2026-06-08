<?php

namespace Modules\User\F23_LikeSystem\Services;

use Modules\User\F23_LikeSystem\Repositories\LikeRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Auth\User;
use Exception;

class LikeService
{
    public function __construct(
        protected LikeRepository $repository,
        protected NotificationService $notificationService,
        protected BadgeAchievementService $gamification
    ) {}

    public function toggleLike(string $userId, string $targetId, string $targetType): array
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        if ($model && $model->user_id === $userId) {
            abort(403, 'Anda tidak bisa menyukai konten Anda sendiri.');
        }

        $isLiked = $this->repository->isLiked($userId, $targetId, $targetType);

        if ($isLiked) {
            $this->repository->removeLike($userId, $targetId, $targetType);
            $this->handlePoints($targetId, $targetType, $userId, false);
            $status = 'unliked';
        } else {
            $this->repository->addLike($userId, $targetId, $targetType);
            $this->handlePoints($targetId, $targetType, $userId, true);
            $this->sendNotification($userId, $targetId, $targetType);
            $status = 'liked';
        }

        return [
            'status' => $status,
            'count'  => $this->repository->getLikeCount($targetId, $targetType)
        ];
    }

    protected function handlePoints(string $targetId, string $targetType, string $actorId, bool $isLike): void
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        if (!$model) return;

        // Jangan kasih poin jika like konten sendiri
        if ($model->user_id === $actorId) return;

        $owner = User::find($model->user_id);
        if (!$owner) return;

        $points = $isLike ? 2 : -2;
        $action = $isLike ? 'like_received' : 'unlike_received';

        $this->gamification->addPoints($owner, $points, $action, $targetId);
    }

    protected function sendNotification(string $actorId, string $targetId, string $targetType): void
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        
        if (!$model) return;

        $ownerId = $model->user_id;

        if ($ownerId !== $actorId) {
            $this->notificationService->createNotification(
                userId: $ownerId,
                actorId: $actorId,
                type: 'like_' . $targetType,
                refId: $targetId,
                refType: $targetType
            );
        }
    }
}