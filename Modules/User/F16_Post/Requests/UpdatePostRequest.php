<?php

namespace Modules\User\F16_Post\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Content\Post; // Import modelnya agar lebih bersih

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gunakan route('post') atau $this->route('post')
        // Pastikan nama parameter di route kamu memang 'post' (sesuai route:list)
        $postId = $this->route('post');

        if (!$postId) {
            return false;
        }

        $post = Post::find($postId);
        
        // Pastikan post ada DAN user yang login adalah pemiliknya
        return $post !== null && $post->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|uuid|exists:categories,id',
            'title'       => 'sometimes|string|max:255',
            'body'        => 'sometimes|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ];
    }
}