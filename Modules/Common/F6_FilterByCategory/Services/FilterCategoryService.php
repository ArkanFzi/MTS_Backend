<?php

namespace Modules\Common\F6_FilterByCategory\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class FilterCategoryService
{
    public function execute(string $categorySlug, int $perPage = 10): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $cacheKey = "category_{$categorySlug}_page_{$page}_perpage_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($categorySlug, $perPage) {
            return Post::query()
                ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
                ->where('status', 'open')
                ->whereHas('category', function ($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }
}