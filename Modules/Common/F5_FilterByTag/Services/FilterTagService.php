<?php

namespace Modules\Common\F5_FilterByTag\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class FilterTagService
{
    public function execute(string $tagSlug, int $perPage = 10, string $sort = 'newest', ?string $category = null): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $cacheKey = "tag_{$tagSlug}_page_{$page}_perpage_{$perPage}_sort_{$sort}_cat_" . ($category ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($tagSlug, $perPage, $sort, $category) {
            $query = Post::query()
                ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
                ->where('status', 'open')
                ->whereHas('tags', function ($query) use ($tagSlug) {
                    $query->where('slug', $tagSlug);
                });

            // Optional category filter
            if ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            // Sort options: newest, bountied (most comments), unanswered (0 comments)
            switch ($sort) {
                case 'unanswered':
                    $query->whereDoesntHave('comments')
                          ->orderBy('created_at', 'desc');
                    break;
                case 'bountied':
                    $query->orderBy('vote_score', 'desc')
                          ->orderBy('view_count', 'desc');
                    break;
                default: // newest
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            return $query->paginate($perPage);
        });
    }
}