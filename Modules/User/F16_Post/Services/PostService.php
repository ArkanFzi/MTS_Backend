<?php

namespace Modules\User\F16_Post\Services;

use Modules\User\F16_Post\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function __construct(protected PostRepository $repo) {}

    public function getPosts() { return $this->repo->getAllPaginated(); }

    public function getPostDetail(string $id)
    {
        $post = $this->repo->findById($id);
        $post->increment('view_count');
        return $post;
    }

    public function createPost(array $data)
    {
        $data['user_id'] = Auth::id();
        $post = $this->repo->create($data);
        
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }
        return $post;
    }

    public function updatePost(string $id, array $data)
{
    // Update data utama
    $post = $this->repo->update($id, $data);

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