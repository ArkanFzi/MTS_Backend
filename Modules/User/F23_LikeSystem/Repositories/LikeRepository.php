<?php

namespace Modules\User\F23_LikeSystem\Repositories;

use App\Models\Interaction\Like;
use Illuminate\Support\Str;

class LikeRepository
{
    public function isLiked(string $userId, string $targetId, string $targetType): bool
    {
        return Like::where('user_id', $userId)
            ->where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->exists();
    }

    public function addLike(string $userId, string $targetId, string $targetType): void
    {
        Like::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => $userId,
            'target_id'   => $targetId,
            'target_type' => $targetType,
        ]);
    }

    public function removeLike(string $userId, string $targetId, string $targetType): void
    {
        Like::where('user_id', $userId)
            ->where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->delete();
    }

    public function getLikeCount(string $targetId, string $targetType): int
    {
        return Like::where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->count();
    }
}