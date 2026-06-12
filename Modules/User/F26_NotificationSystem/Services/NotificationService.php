<?php

namespace Modules\User\F26_NotificationSystem\Services;

use Modules\User\F26_NotificationSystem\Repositories\NotificationRepository;
use App\Models\Moderation\Notification;
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
        // Prevent self-notifications (user interacting with own content)
        if ($userId === $actorId) {
            return;
        }

        // Deduplication: check if a notification already exists for this
        // user + actor + type + reference combination to prevent duplicate rows
        $exists = Notification::where('user_id', $userId)
            ->where('actor_id', $actorId)
            ->where('type', $type)
            ->where('reference_id', $refId)
            ->where('reference_type', $refType)
            ->exists();

        if ($exists) {
            return;
        }

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