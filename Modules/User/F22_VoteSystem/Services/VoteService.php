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

        $delta = 0;

        if (!$existingVote) {
            // Vote baru: +1 untuk upvote, -1 untuk downvote
            $this->repository->createVote($userId, $targetId, $targetType, $typeValue);
            $action = 'voted';
            $delta = $typeValue;
            $this->handlePoints($targetId, $targetType, $typeValue, $userId);
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);

        } elseif ((int) $existingVote->vote_type === $typeValue) {
            // Batalkan vote yang sama: balik arah
            $this->repository->deleteVote($existingVote);
            $action = 'canceled';
            $delta = -$typeValue;
            $this->handlePoints($targetId, $targetType, -$typeValue, $userId);

        } else {
            // Ganti vote (up→down atau down→up): perubahan ±2
            $this->repository->updateVote($existingVote, $typeValue);
            $action = 'updated';
            $delta = $typeValue * 2;
            $this->handlePoints($targetId, $targetType, -(int)$existingVote->vote_type, $userId);
            $this->handlePoints($targetId, $targetType, $typeValue, $userId);
            if ($typeValue === 1) $this->sendNotification($userId, $targetId, $targetType);
        }

        // Terapkan delta pada kolom vote_score (bukan recount penuh dari tabel votes).
        // Ini menjaga konsistensi meski vote_score di-seed langsung tanpa entri di tabel votes.
        $newScore = $this->applyScoreDelta($targetId, $targetType, $delta);

        return ['action' => $action, 'score' => $newScore];
    }

    /**
     * Terapkan delta ke kolom vote_score menggunakan increment/decrement atomik.
     * Cara ini aman terhadap race condition dan tidak bergantung pada jumlah
     * baris di tabel votes (menghindari desync antara kolom dan tabel votes).
     */
    protected function applyScoreDelta(string $targetId, string $targetType, int $delta): int
    {
        if ($targetType === 'post') {
            if ($delta > 0) {
                Post::where('id', $targetId)->increment('vote_score', $delta);
            } elseif ($delta < 0) {
                Post::where('id', $targetId)->decrement('vote_score', abs($delta));
            }
            return (int) Post::where('id', $targetId)->value('vote_score');
        } else {
            if ($delta > 0) {
                Comment::where('id', $targetId)->increment('vote_score', $delta);
            } elseif ($delta < 0) {
                Comment::where('id', $targetId)->decrement('vote_score', abs($delta));
            }
            return (int) Comment::where('id', $targetId)->value('vote_score');
        }
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