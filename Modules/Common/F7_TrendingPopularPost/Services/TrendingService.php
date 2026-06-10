<?php

namespace Modules\Common\F7_TrendingPopularPost\Services;

use App\Models\Content\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TrendingService
{
    public function execute(string $type = 'popular', int $limit = 5): Collection
    {
        $cacheKey = "trending_{$type}_{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($type, $limit) {
            $query = Post::query()
                ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
                ->where('status', 'published');

            if ($type === 'trending') {
                $query->orderBy('vote_score', 'desc');
            } else {
                $query->orderBy('view_count', 'desc');
            }

            return $query->limit($limit)->get();
        });
    }
}