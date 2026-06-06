<?php

namespace Modules\User\F16_Post\Repositories;

use App\Models\Content\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostRepository
{
    public function getAllPaginated(int $perPage = 15)
    {
        $query = Post::with(['user:id,username', 'category:id,name'])
            ->latest();

        // Coba resolve user via sanctum
        $user = auth('sanctum')->user();

        if ($user) {
            // Debugging role
            $isAdmin = $user->hasRole('admin');
            $isModerator = $user->hasRole('moderator');
            
            \Log::info('Checking user roles for post list:', [
                'user_id' => $user->id,
                'isAdmin' => $isAdmin,
                'isModerator' => $isModerator
            ]);

            if (!$isModerator && !$isAdmin) {
                $query->where(function($q) use ($user) {
                    $q->where('status', 'open')
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->where('status', 'closed')
                               ->where('user_id', $user->id);
                      });
                });
            }
        } else {
            $query->where('status', 'open');
        }

        return $query->paginate($perPage);
    }

    public function findById(string $id)
    {
        $query = Post::with(['user', 'category', 'tags', 'comments.user', 'comments.replies.user']);

        // Coba resolve user via sanctum
        $user = auth('sanctum')->user();
        $isStaff = $user && ($user->hasRole('moderator') || $user->hasRole('admin'));

        if ($isStaff) {
            $post = $query->withTrashed()->findOrFail($id);
        } else {
            $post = $query->findOrFail($id);
        }

        // Aturan akses untuk status 'closed'
        if ($post->status === 'closed') {
            $currentUserId = $user ? (string) $user->id : '';
            $postUserId = (string) $post->user_id;
            
            $isOwner = $user && ($currentUserId === $postUserId);
            
            Log::info('Checking access for closed post:', [
                'post_id' => $id,
                'current_user_id' => $currentUserId,
                'post_user_id' => $postUserId,
                'is_owner' => $isOwner,
                'is_staff' => $isStaff
            ]);
            
            if (!$isOwner && !$isStaff) {
                abort(403, 'Post ini tidak dapat diakses.');
            }
        }

        return $post;
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