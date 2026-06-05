<?php

namespace Modules\User\F16_Post\Services;

use Modules\User\F16_Post\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Modules\User\F29_GamificationLeaderboard\Services\GamificationService;

class PostService
{
    public function __construct(
        protected PostRepository $repo,
        protected GamificationService $gamification
    ) {}

    public function getPosts() { return $this->repo->getAllPaginated(); }

    public function getPostDetail(string $id)
    {
        $post = $this->repo->findById($id);
        $post->increment('view_count');
        return $post;
    }

    public function createPost(array $data)
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
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
        $oldBody = $post->body;

        // Update data utama
        $post = $this->repo->update($id, $data);

        // Catat ke history jika body berubah
        if (isset($data['body']) && $data['body'] !== $oldBody) {
            $post->editHistories()->create([
                'edited_by'   => Auth::id(),
                'body_before' => $oldBody,
                'body_after'  => $data['body'],
                'reason'      => $data['edit_reason'] ?? 'Update konten',
                'edited_at'   => now(),
            ]);
        }

        // Sync tags jika ada di request
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post->load('tags');
    }

    public function deletePost(string $id)
    {
        return $this->repo->delete($id);
    }
}