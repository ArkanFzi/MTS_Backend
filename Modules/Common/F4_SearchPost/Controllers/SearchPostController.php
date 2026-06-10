<?php

namespace Modules\Common\F4_SearchPost\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Common\F4_SearchPost\Services\SearchPostService;

class SearchPostController extends Controller
{
    protected SearchPostService $searchService;

    public function __construct(SearchPostService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request): JsonResponse
    {
        // Ambil filter query parameter 'q' (keyword) dan 'per_page'
        $filters = [
            'q'        => $request->query('q'),
            'per_page' => $request->query('per_page', 10),
            'sort'     => $request->query('sort', 'terbaru'),
        ];

        $posts = $this->searchService->execute($filters);

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data postingan.',
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