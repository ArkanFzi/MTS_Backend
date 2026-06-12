<?php

namespace Modules\Common\F6_FilterByCategory\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class FilterCategoryService
{
    public function execute(string $categorySlug, int $perPage = 10, string $sort = 'newest', ?string $tag = null): LengthAwarePaginator
    {
        $page     = request()->get('page', 1);
        $cacheKey = "category_{$categorySlug}_page_{$page}_perpage_{$perPage}_sort_{$sort}_tag_" . ($tag ?? 'all');

        return Cache::remember($cacheKey, 300, function () use ($categorySlug, $perPage, $sort, $tag) {
            $query = Post::query()
                ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
                ->where('status', 'open')
                ->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });

            // Intersection filter: only posts that also have the given tag
            if ($tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('slug', $tag);
                });
            }

            switch ($sort) {
                case 'unanswered':
                    $query->whereDoesntHave('comments')
                          ->orderBy('created_at', 'desc');
                    break;
                case 'bountied':
                    $query->orderBy('vote_score', 'desc')
                          ->orderBy('view_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            return $query->paginate($perPage);
        });
    }
}