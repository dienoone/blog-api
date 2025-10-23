<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(protected ArticleService $articleService) {}

    public function index(Request $request): JsonResponse
    {
        $articles = $this->articleService->getAllArticles($request->all());

        return $this->successResponseWithPagination(ArticleResource::collection($articles));
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->createArticle($request->validated());

        return $this->successResponse(
            new ArticleResource($article),
            'Article created successfully',
            201
        );
    }

    public function show($identifier): JsonResponse
    {
        $article = $this->articleService->getArticle($identifier);

        return $this->successResponse(new ArticleResource($article));
    }


    public function update(UpdateArticleRequest $request, string $article): JsonResponse
    {
        $article = $this->articleService->updateArticle($article, $request->validated());

        return $this->successResponse(
            new ArticleResource($article),
            'Article updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $article): JsonResponse
    {
        $this->articleService->deleteArticle($article);

        return $this->successResponse(null, 'Article deleted successfully');
    }

    public function userArticles(Request $request, $userId): JsonResponse
    {
        $articles = $this->articleService->getUserArticles($userId, $request->all());

        return $this->successResponseWithPagination(ArticleResource::collection($articles));
    }

    public function toggleLike($id): JsonResponse
    {
        $result = $this->articleService->toggleLike($id);

        return $this->successResponse($result, 'Like toggled successfully');
    }

    public function popular(): JsonResponse
    {
        $articles = $this->articleService->getPopularArticles();

        return $this->successResponse(ArticleResource::collection($articles));
    }
}
