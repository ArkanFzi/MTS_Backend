<?php

namespace Modules\User\F17_Comment\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F17_Comment\Services\CommentService;
use Modules\User\F17_Comment\Requests\StoreCommentRequest;
use Modules\User\F17_Comment\Requests\UpdateCommentRequest;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    protected $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function store(StoreCommentRequest $request, string $postId): JsonResponse
    {
        $data = $request->validated();
        $data['post_id'] = $postId;

        $comment = $this->service->addComment($data, auth()->id(), $postId);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan', 
            'data' => $comment
        ], 201);
    }

    // Di CommentController.php

public function update(UpdateCommentRequest $request, string $postId, string $commentId): JsonResponse
{
    try {
        // Panggil service dengan ID komentar yang tepat
        $comment = $this->service->updateComment($commentId, $request->validated(), auth()->id());
        
        return response()->json([
            'message' => 'Komentar berhasil diedit', 
            'data' => $comment
        ]);
    } catch (\Exception $e) {
        // Return 403 atau 400 tergantung jenis error-nya
        return response()->json(['message' => $e->getMessage()], 400);
    }
}

    public function index(string $postId): JsonResponse
    {
        return response()->json($this->service->getComments($postId));
    }
}