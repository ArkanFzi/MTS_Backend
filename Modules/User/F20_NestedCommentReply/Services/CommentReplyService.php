<?php

namespace Modules\User\F20_NestedCommentReply\Services;

use Modules\User\F17_Comment\Repositories\CommentRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use App\Models\Content\Comment;

class CommentReplyService
{
    public function __construct(
        protected CommentRepository $repository,
        protected NotificationService $notificationService
    ) {}

    public function reply(string $userId, string $postId, string $parentId, array $data)
    {
        // Pastikan parent comment ada
        $parentComment = $this->repository->findById($parentId);

        $data['user_id'] = $userId;
        $data['post_id'] = $postId;
        $data['parent_id'] = $parentId;

        $reply = $this->repository->create($data);

        // Notifikasi ke pemilik komentar yang dibalas (parent)
        if ($parentComment->user_id !== $userId) {
            $this->notificationService->createNotification(
                userId: $parentComment->user_id,
                actorId: $userId,
                type: 'comment_reply',
                refId: $reply->id,
                refType: 'comment'
            );
        }

        return $reply;
    }
}
