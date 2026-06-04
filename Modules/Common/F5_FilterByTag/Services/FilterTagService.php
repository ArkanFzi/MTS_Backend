<?php

namespace Modules\Common\F5_FilterByTag\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FilterTagService
{
    public function execute(string $tagSlug, int $perPage = 10): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
            ->where('status', 'published')
            ->whereHas('tags', function ($query) use ($tagSlug) {
                $query->where('slug', $tagSlug);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}