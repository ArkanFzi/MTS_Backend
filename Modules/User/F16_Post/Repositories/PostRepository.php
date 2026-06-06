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

        if (Auth::check()) {
            $user = Auth::user();
            
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
        // Gunakan withTrashed agar staf bisa melihat post yang dihapus
        $query = Post::with(['user', 'category', 'tags', 'comments.user', 'comments.replies.user']);

        $isStaff = Auth::check() && (Auth::user()->hasRole('moderator') || Auth::user()->hasRole('admin'));

        if ($isStaff) {
            $post = $query->withTrashed()->findOrFail($id);
        } else {
            $post = $query->findOrFail($id);
        }

        // Aturan akses untuk status 'closed'
        if ($post->status === 'closed') {
            $isOwner = Auth::check() && $post->user_id === Auth::id();
            
            Log::info('Checking access for closed post:', [
                'post_id' => $id,
                'current_user_id' => Auth::id(),
                'post_user_id' => $post->user_id,
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