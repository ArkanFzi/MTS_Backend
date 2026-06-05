<?php

namespace Modules\User\F18_MarkAcceptedAnswer\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\User\F18_MarkAcceptedAnswer\Services\AcceptedAnswerService;

class AcceptedAnswerController extends Controller
{
    public function __construct(protected AcceptedAnswerService $service) {}

    public function store(string $postId, string $commentId): JsonResponse
    {
        try {
            $this->service->mark(Auth::id(), $postId, $commentId);

            return response()->json([
                'message' => 'Jawaban berhasil ditandai sebagai jawaban terbaik.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ? $e->getStatusCode() : 500);
        }
    }
}