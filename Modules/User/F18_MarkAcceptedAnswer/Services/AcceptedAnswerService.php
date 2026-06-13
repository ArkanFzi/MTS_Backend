<?php

namespace Modules\User\F18_MarkAcceptedAnswer\Services;

use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Auth\User;
use Modules\User\F18_MarkAcceptedAnswer\Repositories\AcceptedAnswerRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcceptedAnswerService
{
    public function __construct(
        protected AcceptedAnswerRepository $repository,
        protected NotificationService $notificationService,
        protected BadgeAchievementService $gamification
    ) {}

    public function mark(string $userId, string $postId, string $commentId): void
    {
        $post = Post::findOrFail($postId);
        $comment = Comment::findOrFail($commentId);

        if ($post->user_id !== $userId) {
            throw new AccessDeniedHttpException('Anda tidak memiliki izin untuk menandai jawaban ini.');
        }

        if ($comment->post_id !== $postId) {
            throw new NotFoundHttpException('Komentar tidak ditemukan pada postingan ini.');
        }

        $this->repository->markAccepted($postId, $commentId);

        // Kirim notifikasi ke pemilik jawaban
        $this->notificationService->createNotification(
            userId: $comment->user_id,
            actorId: $userId,
            type: 'answer_accepted',
            refId: $commentId,
            refType: 'comment'
        );

        // Tambah poin ke pemilik jawaban
        // Jangan kasih poin jika accept jawaban sendiri
        if ($comment->user_id !== $userId) {
            $answerer = User::find($comment->user_id);
            if ($answerer) {
                $this->gamification->addPoints(
                    $answerer,
                    15,
                    'answer_accepted',
                    $commentId,
                    'Jawaban diterima sebagai solusi'
                );
            }
        }
    }
}