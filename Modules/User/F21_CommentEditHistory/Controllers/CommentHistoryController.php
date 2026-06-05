<?php

namespace Modules\User\F21_CommentEditHistory\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\F21_CommentEditHistory\Services\CommentHistoryService;

class CommentHistoryController extends Controller
{
    public function __construct(protected CommentHistoryService $service) {}

    public function index(string $commentId): JsonResponse
    {
        $history = $this->service->getHistory($commentId);

        return response()->json([
            'message' => 'Comment edit history retrieved successfully.',
            'data' => $history
        ], 200);
    }
}