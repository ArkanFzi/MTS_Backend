<?php

namespace Modules\Common\F4_SearchPost\Services;

use App\Models\Content\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchPostService
{
    /**
     * Jalankan query pencarian postingan berdasarkan keyword
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        $keyword = $filters['q'] ?? '';
        $perPage = $filters['per_page'] ?? 10;
        $sort    = $filters['sort'] ?? 'terbaru';

        $query = Post::query()
            ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
            ->where('status', 'open');

        // Jika ada keyword, cari di kolom title atau body
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'ILIKE', '%' . $keyword . '%')
                  ->orWhere('body', 'ILIKE', '%' . $keyword . '%');
            });
        }

        // Urutkan berdasarkan parameter sort
        if ($sort === 'tertinggi') {
            $query->orderBy('vote_score', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }
}