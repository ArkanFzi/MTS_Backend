<?php

namespace Modules\User\F17_Comment\Services;

use Modules\User\F17_Comment\Repositories\CommentRepository;
use App\Models\Content\Post;    // Tambahkan ini
use App\Models\Content\Comment; // Tambahkan ini

use Modules\User\F27_GamificationLeaderboard\Services\GamificationService;

class CommentService
{
    protected $repository;
    protected $gamification;

    public function __construct(CommentRepository $repository, GamificationService $gamification)
    {
        $this->repository = $repository;
        $this->gamification = $gamification;
    }

    // Di Modules\User\F17_Comment\Services\CommentService.php

public function addComment(array $data, string $userId, string $postId)
{
    // 1. Ambil postingan untuk tahu siapa penulisnya
    $post = \App\Models\Content\Post::find($postId);
    
    // 2. Jika yang komen adalah penulis postingan itu sendiri
    if ($post->user_id === $userId) {
        // Hitung berapa komentar yang sudah dibuat user ini di postingan ini
        $commentCount = \App\Models\Content\Comment::where('post_id', $postId)
            ->where('user_id', $userId)
            ->count();
        
        // 3. Batasi maksimal 4 kali
        if ($commentCount >= 4) {
            throw new \Exception('Anda hanya diperbolehkan mengomentari postingan Anda sendiri maksimal 4 kali.');
        }
    }

    $data['user_id'] = $userId;
    $data['post_id'] = $postId;
    
    $comment = $this->repository->create($data);

    // Tambah poin: +5 untuk setiap komentar
    $user = \App\Models\Auth\User::find($userId);
    $this->gamification->addPoints($user, 5, 'create_comment', $comment->id, 'Menulis komentar');

    return $comment;
}

    public function getComments(string $postId)
    {
        return $this->repository->getByPost($postId);
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