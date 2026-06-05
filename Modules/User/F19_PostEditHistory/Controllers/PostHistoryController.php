<?php

namespace Modules\User\F19_PostEditHistory\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\F19_PostEditHistory\Services\PostHistoryService;

class PostHistoryController extends Controller
{
    public function __construct(protected PostHistoryService $service) {}

    public function index(string $postId): JsonResponse
    {
        $history = $this->service->getHistory($postId);

        return response()->json([
            'message' => 'Post edit history retrieved successfully.',
            'data' => $history
        ], 200);
    }
}