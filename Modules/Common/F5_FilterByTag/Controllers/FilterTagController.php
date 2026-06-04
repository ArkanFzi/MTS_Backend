<?php

namespace Modules\Common\F5_FilterByTag\Controllers;

use App\Http\Controllers\Controller;
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
        $perPage = $request->query('per_page', 10);
        $posts = $this->filterTagService->execute($slug, $perPage);

        return response()->json([
            'status'  => 'success',
            'message' => "Berhasil mengambil postingan dengan tag: {$slug}",
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