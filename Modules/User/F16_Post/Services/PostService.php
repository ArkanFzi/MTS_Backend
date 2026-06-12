<?php

namespace Modules\User\F16_Post\Services;

use Modules\User\F16_Post\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;

class PostService
{
    public function __construct(
        protected PostRepository $repo,
        protected BadgeAchievementService $gamification
    ) {}

    public function getPosts(int $perPage = 15, ?string $sort = null) { 
        return $this->repo->getAllPaginated($perPage, $sort); 
    }

    public function getMyPosts(string $userId, int $perPage = 15)
    {
        return $this->repo->getMyPostsPaginated($userId, $perPage);
    }

    public function getPostDetail(string $id)
    {
        $post = $this->repo->findById($id);
        $post->increment('view_count');
        return $post;
    }

    public function createPost(array $data)
{
    $user = Auth::user();

    // Cek minimum poin
    if ($user->reputation_points < 15) {
        abort(403, 'Anda membutuhkan minimal 15 poin untuk membuat post.');
    }

    $data['user_id'] = $user->id;
    $data['status']  = 'open';
    
    $post = $this->repo->create($data);
    
    if (isset($data['tags'])) {
        $post->tags()->sync($data['tags']);
    }

    $this->gamification->addPoints($user, 10, 'create_post', $post->id, 'Membuat postingan baru');

    return $post;
}

    public function updatePost(string $id, array $data)
{
    $post = $this->repo->findById($id);
    $user = Auth::user();

    // Cek izin: pemilik, moderator, atau admin
    if ($post->user_id !== $user->id && !$user->hasRole('moderator') && !$user->hasRole('admin')) {
        abort(403, 'Anda tidak memiliki izin untuk mengedit post ini.');
    }

    // Rate limit: hanya berlaku untuk pemilik (bukan mod/admin)
    if ($post->user_id === $user->id && !$user->hasRole('moderator') && !$user->hasRole('admin')) {
        $this->checkEditRateLimit($post);
    }

    $oldBody = $post->body;

    $post = $this->repo->update($id, $data);

    if (isset($data['body']) && $data['body'] !== $oldBody) {
        $post->editHistories()->create([
            'edited_by'   => $user->id,
            'body_before' => $oldBody,
            'body_after'  => $data['body'],
            'reason'      => $data['edit_reason'] ?? 'Update konten',
            'edited_at'   => now(),
        ]);
    }

    if (isset($data['tags'])) {
        $post->tags()->sync($data['tags']);
    }

    return $post->load('tags');
}

private function checkEditRateLimit($post): void
{
    $editCount = $post->editHistories()
        ->where('edited_by', Auth::id())
        ->where('edited_at', '>=', now()->subMinutes(30))
        ->count();

    if ($editCount >= 3) {
        abort(429, 'Anda telah mencapai batas maksimal edit (3 kali dalam 30 menit). Silakan coba lagi nanti.');
    }
}

    public function updateStatus(string $id, string $status)
    {
        $post = $this->repo->findById($id);

        // Cek izin: Hanya pemilik atau staf yang boleh update status
        if ($post->user_id !== Auth::id() && !Auth::user()->hasRole('moderator') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status post ini.');
        }

        return $this->repo->update($id, ['status' => $status]);
    }

    public function deletePost(string $id)
{
    $post = $this->repo->findById($id);

    if ($post->user_id !== Auth::id() && !Auth::user()->hasRole('moderator') && !Auth::user()->hasRole('admin')) {
        abort(403, 'Anda tidak memiliki izin untuk menghapus post ini.');
    }

    return $this->repo->delete($id);
}
}