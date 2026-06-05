<?php

namespace Modules\User\F16_Post\Repositories;

use App\Models\Content\Post;

class PostRepository
{
    public function getAllPaginated(int $perPage = 15)
    {
        // Ganti 'user:id,nama' menjadi 'user:id,username'
return Post::with(['user:id,username', 'category:id,name'])
    ->latest()
    ->paginate($perPage);
    }

    public function findById(string $id)
    {
        return Post::with(['user', 'category', 'tags', 'comments'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Post::create($data);
    }

    public function update(string $id, array $data)
    {
        $post = Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function delete(string $id)
    {
        return Post::findOrFail($id)->delete();
    }
}