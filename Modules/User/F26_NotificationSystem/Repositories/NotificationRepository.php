<?php

namespace Modules\User\F26_NotificationSystem\Repositories;

use App\Models\Moderation\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository
{
    public function getUserNotifications(string $userId, bool $unreadOnly = false): LengthAwarePaginator
    {
        $query = Notification::where('user_id', $userId)
            ->with(['actor:id,username,avatar_url', 'reference'])
            ->latest();

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->paginate(15);
    }

    public function markAsRead(string $notificationId, string $userId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    public function markAllAsRead(string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }
}