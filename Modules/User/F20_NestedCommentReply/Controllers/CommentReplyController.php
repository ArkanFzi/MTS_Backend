<?php

namespace Modules\User\F20_NestedCommentReply\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F17_Comment\Requests\StoreCommentRequest;
use Modules\User\F17_Comment\Requests\UpdateCommentRequest;
use Modules\User\F17_Comment\Services\CommentService;
use Modules\User\F20_NestedCommentReply\Services\CommentReplyService;
use Illuminate\Http\JsonResponse;

class CommentReplyController extends Controller
{
    public function __construct(
        protected CommentReplyService $service,
        protected CommentService $commentService
    ) {}

    public function store(StoreCommentRequest $request, string $postId, string $commentId): JsonResponse
    {
        $reply = $this->service->reply(
            auth()->id(),
            $postId,
            $commentId,
            $request->validated()
        );

        return response()->json([
            'message' => 'Balasan berhasil ditambahkan',
            'data' => $reply
        ], 201);
    }

    public function update(UpdateCommentRequest $request, string $postId, string $commentId, string $replyId): JsonResponse
    {
        try {
            // Kita gunakan service dari F17 untuk update karena logicnya sama
            $comment = $this->commentService->updateComment($replyId, $request->validated(), auth()->id());
            
            return response()->json([
                'message' => 'Balasan berhasil diedit', 
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
