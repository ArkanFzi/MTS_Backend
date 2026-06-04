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

        $query = Post::query()
            ->with(['user:id,username,avatar_url', 'category:id,name,slug', 'tags:id,name,slug,color'])
            ->where('status', 'published'); // Pastiin cuma post yang udah rilis yang dicari

        // Jika ada keyword, cari di kolom title atau body
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'ILIKE', '%' . $keyword . '%')
                  ->orWhere('body', 'ILIKE', '%' . $keyword . '%');
            });
        }

        // Urutkan dari yang terbaru, lalu paginasikan hasilnya
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}