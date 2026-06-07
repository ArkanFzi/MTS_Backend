<?php

namespace Modules\User\F16_Post\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F16_Post\Services\PostService;
use Modules\User\F16_Post\Requests\StorePostRequest;
use Modules\User\F16_Post\Requests\UpdatePostRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(protected PostService $service) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $sort = $request->query('sort');
        
        return response()->json([
            'success' => true, 
            'data' => $this->service->getPosts((int)$perPage, $sort)
        ]);
    }

    public function myPosts(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $userId = $request->user()->id;
        
        return response()->json([
            'success' => true, 
            'data' => $this->service->getMyPosts($userId, (int)$perPage)
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getPostDetail($id)]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->service->createPost($request->validated());
        return response()->json(['success' => true, 'message' => 'Post berhasil dibuat', 'data' => $post], 201);
    }

    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        $post = $this->service->updatePost($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Post berhasil diperbarui', 'data' => $post]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,closed'
        ]);

        $this->service->updateStatus($id, $request->status);
        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->deletePost($id);
        return response()->json(['success' => true, 'message' => 'Post berhasil dihapus']);
    }
}