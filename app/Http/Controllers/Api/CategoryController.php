<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories($request->all());

        return $this->successResponseWithPagination($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    public function show(string $identifier): JsonResponse
    {
        $category = $this->categoryService->getCategory($identifier);

        return $this->successResponse(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return $this->successResponse(null, 'Category deleted successfully');
    }
}
