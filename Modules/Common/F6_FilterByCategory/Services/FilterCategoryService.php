<?php

namespace Modules\Common\F6_FilterByCategory\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FilterCategoryService
{
    public function execute(string $categorySlug, int $perPage = 10): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
            ->where('status', 'published')
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}