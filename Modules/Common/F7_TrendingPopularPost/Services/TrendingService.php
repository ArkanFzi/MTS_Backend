<?php

namespace Modules\Common\F7_TrendingPopularPost\Services;

use App\Models\Content\Post;
use Illuminate\Database\Eloquent\Collection;

class TrendingService
{
    public function execute(string $type = 'popular', int $limit = 5): Collection
    {
        $query = Post::query()
            ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
            ->where('status', 'published');

        // Tentukan sorting berdasarkan parameter tipe
        if ($type === 'trending') {
            $query->orderBy('vote_score', 'desc');
        } else {
            $query->orderBy('view_count', 'desc');
        }

        return $query->limit($limit)->get();
    }
}