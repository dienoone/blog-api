<?php

namespace App\Services;

use App\Models\Category;
use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;
use App\Exceptions\ForbiddenException;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CategoryService
{
  public function getAllCategories(array $filters = [])
  {
    $query = Category::query();

    // Search by name
    if (!empty($filters['search'])) {
      $query->where('name', 'like', '%' . $filters['search'] . '%');
    }

    // Order by
    $orderBy = $filters['order_by'] ?? 'name';
    $orderDirection = $filters['order_direction'] ?? 'asc';
    $query->orderBy($orderBy, $orderDirection);

    // Pagination
    $perPage = $filters['per_page'] ?? 15;
    return $query->paginate($perPage);
  }

  public function getCategory($identifier)
  {
    $category = is_numeric($identifier)
      ? Category::find($identifier)
      : Category::where('slug', $identifier)->first();

    throw_if(!$category, NotFoundException::class, 'Category not found');

    return $category->load('articles');
  }

  public function createCategory(array $data): Category
  {
    return DB::transaction(function () use ($data) {
      // Generate slug if not provided
      $data['slug'] ??= Str::slug($data['name']);

      // Check if slug already exists
      if (Category::where('slug', $data['slug'])->exists()) {
        throw new ConflictException('Category with this slug already exists');
      }

      return Category::create($data);
    });
  }

  public function updateCategory($id, array $data): Category
  {
    $category = Category::find($id);
    throw_if(!$category, NotFoundException::class, 'Category not found');

    return DB::transaction(function () use ($category, $data) {
      // Update slug if name changed
      if (isset($data['name']) && !isset($data['slug'])) {
        $data['slug'] = Str::slug($data['name']);
      }

      // Check if slug already exists (excluding current category)
      if (isset($data['slug'])) {
        $exists = Category::where('slug', $data['slug'])
          ->where('id', '!=', $category->id)
          ->exists();

        if ($exists) {
          throw new ConflictException('Category with this slug already exists');
        }
      }

      $category->update($data);
      return $category->fresh();
    });
  }

  public function deleteCategory($id): void
  {
    $category = Category::find($id);
    throw_if(!$category, NotFoundException::class, 'Category not found');

    throw_if(
      $category->articles()->exists(),
      ConflictHttpException::class,
      'Cannot delete category with existing articles'
    );

    $category->delete();
  }
}
