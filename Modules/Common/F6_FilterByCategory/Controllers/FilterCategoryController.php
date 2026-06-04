<?php

namespace Modules\Common\F6_FilterByCategory\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $posts = $this->filterCategoryService->execute($slug, $perPage);

        return response()->json([
            'status'  => 'success',
            'message' => "Berhasil mengambil postingan dengan kategori: {$slug}",
            'data'    => $posts->items(),
            'meta'    => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ]
        ], 200);
    }
}