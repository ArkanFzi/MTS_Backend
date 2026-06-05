<?php

namespace Modules\User\F22_VoteSystem\Repositories;

use App\Models\Interaction\Vote;
use Illuminate\Support\Str;

class VoteRepository
{
    public function getExistingVote(string $userId, string $targetId, string $targetType): ?Vote
    {
        return Vote::where('user_id', $userId)
            ->where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->first();
    }

    public function createVote(string $userId, string $targetId, string $targetType, int $type): void
    {
        Vote::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'target_id' => $targetId,
            'target_type' => $targetType,
            'vote_type' => $type,
        ]);
    }

    public function updateVote(Vote $vote, int $newType): void
    {
        $vote->update(['vote_type' => $newType]);
    }

    public function deleteVote(Vote $vote): void
    {
        $vote->delete();
    }

    public function getScore(string $targetId, string $targetType): int
    {
        return (int) Vote::where('target_id', $targetId)
            ->where('target_type', $targetType)
            ->sum(\Illuminate\Support\Facades\DB::raw('CAST(vote_type AS INTEGER)'));
    }
}