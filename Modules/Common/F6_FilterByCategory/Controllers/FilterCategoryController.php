<?php

namespace Modules\Common\F6_FilterByCategory\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content\Category;
use App\Models\Content\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Common\F6_FilterByCategory\Services\FilterCategoryService;

class FilterCategoryController extends Controller
{
    protected FilterCategoryService $filterCategoryService;

    public function __construct(FilterCategoryService $filterCategoryService)
    {
        $this->filterCategoryService = $filterCategoryService;
    }

    public function filter(Request $request, string $slug): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $sort    = $request->query('sort', 'newest');
        $tag     = $request->query('tag');

        $posts    = $this->filterCategoryService->execute($slug, (int) $perPage, $sort, $tag);
        $category = Category::where('slug', $slug)->first();

        // Fetch all tags that have open posts in this category (for sidebar)
        $tags = Tag::whereHas('posts', function ($q) use ($slug) {
                $q->whereHas('category', fn($c) => $c->where('slug', $slug))
                  ->where('status', 'open');
            })
            ->select('id', 'name', 'slug', 'color')
            ->withCount(['posts as count' => function ($q) use ($slug) {
                $q->whereHas('category', fn($c) => $c->where('slug', $slug))
                  ->where('status', 'open');
            }])
            ->orderByDesc('count')
            ->get()
            ->map(fn(Tag $t) => [
                'id'    => $t->id,
                'name'  => $t->name,
                'slug'  => $t->slug,
                'color' => $t->color,
                'count' => $t->count,
            ]);

        return response()->json([
            'status'   => 'success',
            'message'  => "Berhasil mengambil postingan dengan kategori: {$slug}",
            'data'     => $posts->items(),
            'category' => $category ? [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'description' => $category->description,
                'created_at'  => $category->created_at,
            ] : null,
            'tags'     => $tags,
            'meta'     => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ]
        ], 200);
    }

    /**
     * Returns all categories with their top 5 tags (by post count in that category).
     * Used by the CategoriesListPage discovery page.
     */
    public function categoriesWithTags(): JsonResponse
    {
        $data = Cache::remember('categories_with_tags', 3600, function () {
            return Category::query()
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get()
                ->map(function (Category $category) {
                    // Get tags that have open posts in this category, sorted by usage
                    $tags = Tag::whereHas('posts', function ($q) use ($category) {
                        $q->where('category_id', $category->id)
                          ->where('status', 'open');
                    })
                    ->select('id', 'name', 'slug', 'color')
                    ->withCount(['posts' => function ($q) use ($category) {
                        $q->where('category_id', $category->id)
                          ->where('status', 'open');
                    }])
                    ->orderByDesc('posts_count')
                    ->limit(5)
                    ->get();

                    $postCount = $category->posts()->where('status', 'open')->count();

                    return [
                        'id'          => $category->id,
                        'name'        => $category->name,
                        'slug'        => $category->slug,
                        'description' => $category->description,
                        'post_count'  => $postCount,
                        'tags'        => $tags->map(fn(Tag $t) => [
                            'id'    => $t->id,
                            'name'  => $t->name,
                            'slug'  => $t->slug,
                            'color' => $t->color,
                            'posts_count' => $t->posts_count,
                        ]),
                    ];
                })
                ->values();
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}