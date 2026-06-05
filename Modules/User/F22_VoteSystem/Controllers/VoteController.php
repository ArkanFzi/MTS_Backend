<?php

namespace Modules\User\F22_VoteSystem\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F22_VoteSystem\Requests\VoteRequest;
use Modules\User\F22_VoteSystem\Services\VoteService;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function __construct(protected VoteService $service) {}

    public function vote(VoteRequest $request): JsonResponse
    {
        $result = $this->service->vote(
            auth()->id(),
            $request->target_id,
            $request->target_type,
            $request->type
        );

        return response()->json([
            'message' => "Vote {$result['action']} successfully.",
            'new_score' => $result['score']
        ]);
    }
}