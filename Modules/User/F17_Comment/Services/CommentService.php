<?php

namespace Modules\User\F17_Comment\Services;

use Modules\User\F17_Comment\Repositories\CommentRepository;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;
use Modules\User\F26_NotificationSystem\Services\NotificationService;

class CommentService
{
    protected $repository;
    protected $gamification;
    protected $notification;

    public function __construct(
        CommentRepository $repository, 
        BadgeAchievementService $gamification,
        NotificationService $notification
    ) {
        $this->repository = $repository;
        $this->gamification = $gamification;
        $this->notification = $notification;
    }

    public function addComment(array $data, string $userId, string $postId)
    {
        $post = Post::find($postId);
        
        if ($post->user_id === $userId) {
            $commentCount = Comment::where('post_id', $postId)
                ->where('user_id', $userId)
                ->count();
            
            if ($commentCount >= 4) {
                throw new \Exception('Anda hanya diperbolehkan mengomentari postingan Anda sendiri maksimal 4 kali.');
            }
        }

        $data['user_id'] = $userId;
        $data['post_id'] = $postId;
        
        $comment = $this->repository->create($data);

        $user = \App\Models\Auth\User::find($userId);
        $this->gamification->addPoints($user, 5, 'create_comment', $comment->id, 'Menulis komentar');

        // Notifikasi ke pemilik postingan jika bukan komentator sendiri
        if ($post->user_id !== $userId) {
            $this->notification->createNotification(
                userId: $post->user_id,
                actorId: $userId,
                type: 'new_comment',
                refId: $postId,
                refType: 'post'
            );
        }

        return $comment;
    }

    public function getComments(string $postId)
    {
        return $this->repository->getByPost($postId);
    }

public function deleteComment(string $commentId, string $userId)
{
    $comment = $this->repository->findById($commentId);

    if (!$comment) {
        abort(404, 'Komentar tidak ditemukan.');
    }

    // Hanya moderator atau admin yang boleh hapus
    if (!auth()->user()->hasRole('moderator') && !auth()->user()->hasRole('admin')) {
        abort(403, 'Anda tidak memiliki izin untuk menghapus komentar.');
    }

    return $this->repository->delete($commentId);
}
    
    // Di Modules\User\F17_Comment\Services\CommentService.php

public function updateComment(string $commentId, array $data, string $userId)
{
    $comment = $this->repository->findById($commentId);

    // 1. Cek kepemilikan
    if ($comment->user_id !== $userId) {
        throw new \Exception('Anda tidak memiliki izin untuk mengedit komentar ini.');
    }

    // 2. Cek batasan waktu (30 menit)
    // $comment->created_at adalah waktu saat komentar dibuat
    if ($comment->created_at->addMinutes(30)->isPast()) {
        throw new \Exception('Komentar tidak dapat diedit karena sudah melewati batas waktu 30 menit.');
    }

    // 3. Cek apakah sudah pernah di-edit
    if ($comment->editHistories()->count() >= 1) {
        throw new \Exception('Komentar ini hanya bisa diedit satu kali.');
    }

    // 4. Lakukan Update
    $oldBody = $comment->body;
    $comment->update(['body' => $data['body']]);

    // 5. Catat ke History
    $comment->editHistories()->create([
        'comment_id'  => $comment->id,
        'edited_by'   => $userId,
        'body_before' => $oldBody,
        'body_after'  => $data['body'],
        'edited_at'   => now(),
    ]);

    return $comment;
}
}