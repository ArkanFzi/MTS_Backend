<?php

namespace Modules\User\F26_NotificationSystem\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();

        // Safety check: ensure authenticated user ID is valid
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $unreadOnly = $request->boolean('unread_only', false);
        $notifications = $this->service->getNotifications($userId, $unreadOnly);

        return response()->json($notifications);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $updated = $this->service->markAsRead($id, auth()->id());
        
        return response()->json(['success' => (bool) $updated]);
    }

    public function markAllRead(): JsonResponse
    {
        $count = $this->service->markAllAsRead(auth()->id());
        
        return response()->json(['message' => "Marked $count notifications as read."]);
    }
}