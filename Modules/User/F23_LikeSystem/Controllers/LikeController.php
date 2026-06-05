<?php

namespace Modules\User\F23_LikeSystem\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F23_LikeSystem\Requests\ToggleLikeRequest;
use Modules\User\F23_LikeSystem\Services\LikeService;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function __construct(protected LikeService $service) {}

    public function toggle(ToggleLikeRequest $request): JsonResponse
    {
        $data = $this->service->toggleLike(
            auth()->id(),
            $request->target_id,
            $request->target_type
        );

        return response()->json([
            'message' => "Successfully {$data['status']} the content.",
            'status'  => $data['status'],
            'count'   => $data['count']
        ]);
    }
}