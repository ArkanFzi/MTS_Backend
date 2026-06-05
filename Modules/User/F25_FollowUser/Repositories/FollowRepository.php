<?php

namespace Modules\User\F25_FollowUser\Repositories;

use App\Models\Interaction\Follow;
use Illuminate\Database\Eloquent\Collection;

class FollowRepository
{
    public function isFollowing(string $followerId, string $followingId): bool
    {
        return Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->exists();
    }

    public function follow(string $followerId, string $followingId): void
    {
        Follow::create([
            'follower_id' => $followerId,
            'following_id' => $followingId,
        ]);
    }

    public function unfollow(string $followerId, string $followingId): void
    {
        Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->delete();
    }

    public function getFollowers(string $userId): Collection
    {
        return Follow::where('following_id', $userId)->with('follower')->get();
    }

    public function getFollowing(string $userId): Collection
    {
        return Follow::where('follower_id', $userId)->with('following')->get();
    }
}