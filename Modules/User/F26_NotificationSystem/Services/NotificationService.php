<?php

namespace Modules\User\F26_NotificationSystem\Services;

use Modules\User\F26_NotificationSystem\Repositories\NotificationRepository;
use Illuminate\Support\Str;

class NotificationService
{
    protected NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createNotification(
        string $userId, 
        string $actorId, 
        string $type, 
        string $refId, 
        string $refType
    ): void {
        $this->repository->create([
            'id'            => (string) Str::uuid(),
            'user_id'       => $userId,
            'actor_id'      => $actorId,
            'type'          => $type,
            'reference_id'  => $refId,
            'reference_type'=> $refType,
            'is_read'       => false,
        ]);
    }

    public function getNotifications(string $userId, bool $unreadOnly)
    {
        return $this->repository->getUserNotifications($userId, $unreadOnly);
    }

    public function markAsRead(string $notificationId, string $userId): bool
    {
        return $this->repository->markAsRead($notificationId, $userId);
    }

    public function markAllAsRead(string $userId): int
    {
        return $this->repository->markAllAsRead($userId);
    }
}