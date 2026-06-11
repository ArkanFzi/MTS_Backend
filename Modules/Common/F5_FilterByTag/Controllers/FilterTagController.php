<?php

namespace Modules\Common\F5_FilterByTag\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content\Category;
use App\Models\Content\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Common\F5_FilterByTag\Services\FilterTagService;

class FilterTagController extends Controller
{
    protected FilterTagService $filterTagService;

    public function __construct(FilterTagService $filterTagService)
    {
        $this->filterTagService = $filterTagService;
    }

    public function filter(Request $request, string $slug): JsonResponse
    {
        $perPage  = $request->query('per_page', 10);
        $sort     = $request->query('sort', 'newest');
        $category = $request->query('category');

        $posts = $this->filterTagService->execute($slug, (int) $perPage, $sort, $category);
        $tag   = Tag::where('slug', $slug)->first();

        // Get distinct categories that have posts with this tag
        $categories = Category::whereHas('posts', function ($q) use ($slug) {
            $q->whereHas('tags', fn($t) => $t->where('slug', $slug))
              ->where('status', 'open');
        })->select('id', 'name', 'slug')->get();

        return response()->json([
            'status'     => 'success',
            'message'    => "Berhasil mengambil postingan dengan tag: {$slug}",
            'data'       => $posts->items(),
            'tag'        => $tag ? [
                'id'          => $tag->id,
                'name'        => $tag->name,
                'slug'        => $tag->slug,
                'color'       => $tag->color,
                'usage_count' => $tag->usage_count,
                'created_at'  => $tag->created_at,
            ] : null,
            'categories' => $categories,
            'meta'       => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ]
        ], 200);
    }
}