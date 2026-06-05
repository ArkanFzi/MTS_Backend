<?php

namespace Modules\User\F25_FollowUser\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F25_FollowUser\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    public function __construct(protected FollowService $service) {}

    public function toggle(string $userId): JsonResponse
    {
        $status = $this->service->toggleFollow(auth()->id(), $userId);
        return response()->json(['message' => "Successfully $status user."]);
    }

    public function followers(string $userId): JsonResponse
    {
        return response()->json($this->service->getFollowers($userId));
    }

    public function following(string $userId): JsonResponse
    {
        return response()->json($this->service->getFollowing($userId));
    }
}