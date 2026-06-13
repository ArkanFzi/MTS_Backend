<?php

namespace Modules\User\F25_FollowUser\Services;

use Modules\User\F25_FollowUser\Repositories\FollowRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Validation\ValidationException;

class FollowService
{
    public function __construct(
        protected FollowRepository $repository,
        protected NotificationService $notificationService
    ) {}

    public function toggleFollow(string $followerId, string $followingId): string
    {
        if ($followerId === $followingId) {
            throw ValidationException::withMessages(['message' => 'Anda tidak bisa mem-follow diri sendiri.']);
        }

        if ($this->repository->isFollowing($followerId, $followingId)) {
            $this->repository->unfollow($followerId, $followingId);
            return 'unfollowed';
        }

        $this->repository->follow($followerId, $followingId);
        
        // Panggil Notification Service
        $this->notificationService->createNotification(
            userId: $followingId,
            actorId: $followerId,
            type: 'followed',
            refId: $followerId,
            refType: 'user'
        );

        return 'followed';
    }

    public function getFollowers(string $userId) { return $this->repository->getFollowers($userId); }
    public function getFollowing(string $userId) { return $this->repository->getFollowing($userId); }
}