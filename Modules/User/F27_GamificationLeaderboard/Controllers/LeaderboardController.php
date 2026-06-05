<?php

namespace Modules\User\F27_GamificationLeaderboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\F27_GamificationLeaderboard\Services\GamificationService;

class LeaderboardController extends Controller
{
    protected $service;

    public function __construct(GamificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);
        $leaderboard = $this->service->getLeaderboard((int)$limit);

        return response()->json([
            'success' => true,
            'message' => 'Leaderboard berhasil diambil',
            'data'    => $leaderboard->items(),
            'meta'    => [
                'current_page' => $leaderboard->currentPage(),
                'last_page'    => $leaderboard->lastPage(),
                'total'        => $leaderboard->total(),
            ]
        ]);
    }
}
