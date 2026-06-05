<?php

namespace Modules\User\F23_LikeSystem\Services;

use Modules\User\F23_LikeSystem\Repositories\LikeRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use App\Models\Content\Post;
use App\Models\Content\Comment; 
use Exception;

class LikeService
{
    public function __construct(
        protected LikeRepository $repository,
        protected NotificationService $notificationService
    ) {}

    public function toggleLike(string $userId, string $targetId, string $targetType): array
    {
        $isLiked = $this->repository->isLiked($userId, $targetId, $targetType);

        if ($isLiked) {
            $this->repository->removeLike($userId, $targetId, $targetType);
            $status = 'unliked';
        } else {
            $this->repository->addLike($userId, $targetId, $targetType);
            $status = 'liked';

            // Kirim notifikasi jika aksi adalah 'liked'
            $this->sendNotification($userId, $targetId, $targetType);
        }

        return [
            'status' => $status,
            'count'  => $this->repository->getLikeCount($targetId, $targetType)
        ];
    }

    protected function sendNotification(string $actorId, string $targetId, string $targetType): void
    {
        $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);
        
        if (!$model) {
            return; // Atau lempar Exception: Content not found
        }

        $ownerId = $model->user_id;

        // Jangan kirim notif jika user me-like konten miliknya sendiri
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