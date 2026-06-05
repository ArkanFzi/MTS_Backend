<?php

namespace Modules\User\F24_BookmarkPost\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\User\F24_BookmarkPost\Services\BookmarkService;

class BookmarkController extends Controller
{
    public function __construct(protected BookmarkService $service) {}

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|uuid|exists:posts,id'
        ]);

        $status = $this->service->toggleBookmark(Auth::id(), $validated['post_id']);

        return response()->json([
            'message' => "Post successfully {$status}.",
            'status'  => $status
        ], 200);
    }

    public function index(): JsonResponse
    {
        // Panggil service, bukan model langsung
        $bookmarks = $this->service->getMyBookmarks(Auth::id());

        return response()->json([
            'message' => 'Success retrieve bookmarks',
            'data' => $bookmarks
        ], 200);
    }
}