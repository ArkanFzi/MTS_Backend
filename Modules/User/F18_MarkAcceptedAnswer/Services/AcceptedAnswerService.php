<?php

namespace Modules\User\F18_MarkAcceptedAnswer\Services;

use App\Models\Content\Post;
use App\Models\Content\Comment;
use Modules\User\F18_MarkAcceptedAnswer\Repositories\AcceptedAnswerRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcceptedAnswerService
{
    public function __construct(
        protected AcceptedAnswerRepository $repository,
        protected NotificationService $notificationService
    ) {}

    public function mark(string $userId, string $postId, string $commentId): void
    {
        $post = Post::findOrFail($postId);
        $comment = Comment::findOrFail($commentId);

        // Validasi: Apakah user adalah pemilik post?
        if ($post->user_id !== $userId) {
            throw new AccessDeniedHttpException('Anda tidak memiliki izin untuk menandai jawaban ini.');
        }

        // Validasi: Pastikan komentar milik post tersebut
        if ($comment->post_id !== $postId) {
            throw new NotFoundHttpException('Komentar tidak ditemukan pada postingan ini.');
        }

        // Eksekusi update
        $this->repository->markAccepted($postId, $commentId);

        // Kirim Notifikasi ke pemilik komentar
        $this->notificationService->createNotification(
            userId: $comment->user_id,
            actorId: $userId,
            type: 'accepted_answer',
            refId: $commentId,
            refType: 'comment'
        );
    }
}