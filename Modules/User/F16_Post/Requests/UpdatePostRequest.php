<?php

namespace Modules\User\F16_Post\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Content\Post;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $postId = $this->route('post');

        if (!$postId) {
            return false;
        }

        $post = Post::find($postId);

        if (!$post) {
            return false;
        }

        $user = $this->user();

        return $post->user_id === $user->id
            || $user->hasRole('moderator')
            || $user->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|uuid|exists:categories,id',
            'title'       => 'sometimes|string|max:255',
            'body'        => 'sometimes|string',
            'edit_reason' => 'nullable|string|max:255',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string',
        ];
    }
}