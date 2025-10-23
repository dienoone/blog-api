<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TagController extends Controller
{
    public function __construct(protected TagService $tagService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $filters['with_count'] = true;

        $tags = $this->tagService->getAllTags($filters);

        return $tags instanceof LengthAwarePaginator
            ?  $this->successResponseWithPagination(TagResource::collection($tags))
            : $this->successResponse($tags);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->tagService->createTag($request->validated());

        return $this->successResponse(
            new TagResource($tag),
            'Tag created successfully',
            201
        );
    }

    public function update(UpdateTagRequest $request, string $id): JsonResponse
    {
        $tag = $this->tagService->updateTag($id, $request->validated());

        return $this->successResponse(
            new TagResource($tag),
            'Tag updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->tagService->deleteTag($id);

        return $this->successResponse(null, 'Tag deleted successfully');
    }
}
