<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService) {}

    public function index(Request $request, $articleId): JsonResponse
    {
        $comments = $this->commentService->getArticleComments($articleId, $request->all());

        return $this->successResponse(CommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->createComment($request->validated());

        return $this->successResponse(
            new CommentResource($comment),
            'Comment created successfully',
            201
        );
    }

    public function update(UpdateCommentRequest $request, string $id): JsonResponse
    {
        $comment = $this->commentService->updateComment($id, $request->validated());

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->commentService->deleteComment($id);

        return $this->successResponse(null, 'Comment deleted successfully');
    }

    public function toggleLike($id): JsonResponse
    {
        $result = $this->commentService->toggleLike($id);

        return $this->successResponse($result, 'Like toggled successfully');
    }
}
