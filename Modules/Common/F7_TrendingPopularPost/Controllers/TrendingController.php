<?php

namespace Modules\Common\F7_TrendingPopularPost\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Common\F7_TrendingPopularPost\Services\TrendingService;

class TrendingController extends Controller
{
    protected TrendingService $trendingService;

    public function __construct(TrendingService $trendingService)
    {
        $this->trendingService = $trendingService;
    }

    public function getTrending(Request $request): JsonResponse
    {
        // 'type' bisa bernilai 'popular' atau 'trending' (default: popular)
        $type = $request->query('type', 'popular'); 
        $limit = $request->query('limit', 5);

        $posts = $this->trendingService->execute($type, (int) $limit);

        return response()->json([
            'status'  => 'success',
            'message' => "Berhasil mengambil data postingan " . ucfirst($type),
            'data'    => $posts
        ], 200);
    }
}