<?php

namespace App\Services;

use App\Models\Tag;
use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;
use App\Exceptions\ForbiddenException;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagService
{
  public function getAllTags(array $filters = [])
  {
    $query = Tag::query();

    // Search by name
    if (!empty($filters['search'])) {
      $query->where('name', 'like', "%{$filters['search']}%");
    }

    // With article count
    if (!empty($filters['with_count'])) {
      $query->withCount('articles');
    }

    // Order by
    $orderBy = $filters['order_by'] ?? 'name';
    $orderDirection = $filters['order_direction'] ?? 'asc';
    $query->orderBy($orderBy, $orderDirection);

    // Get all or paginate
    if (!empty($filters['all'])) {
      return $query->get();
    }

    $perPage = $filters['per_page'] ?? 15;
    return $query->paginate($perPage);
  }

  public function getOrCreateTags(array $tagNames): array
  {
    $tags = [];

    foreach ($tagNames as $tagName) {
      $slug = Str::slug($tagName);

      $tag = Tag::firstOrCreate(
        ['slug' => $slug],
        ['name' => $tagName]
      );

      $tags[] = $tag->id;
    }

    return $tags;
  }

  public function getTag($identifier)
  {
    $tag = is_numeric($identifier)
      ? Tag::find($identifier)
      : Tag::where('slug', $identifier)->first();

    throw_if(!$tag, NotFoundException::class, 'Tag not found');

    return $tag;
  }

  public function createTag(array $data): Tag
  {
    // Check if user can create tags
    throw_if(
      !Auth::check(),
      ForbiddenException::class,
      'You do not have permission to create tags'
    );

    return DB::transaction(function () use ($data) {
      $data['slug'] ??= Str::slug($data['name']);

      if (Tag::where('slug', $data['slug'])->exists()) {
        throw new ConflictException('Tag with this slug already exists');
      }

      return Tag::create($data);
    });
  }

  public function updateTag($id, array $data): Tag
  {
    throw_if(
      !Auth::check(),
      ForbiddenException::class,
      'Only administrators can update tags'
    );

    $tag = Tag::find($id);
    throw_if(!$tag, NotFoundException::class, 'Tag not found');

    return DB::transaction(function () use ($tag, $data) {
      if (isset($data['name']) && !isset($data['slug'])) {
        $data['slug'] = Str::slug($data['name']);
      }

      if (isset($data['slug'])) {
        $exists = Tag::where('slug', $data['slug'])
          ->where('id', '!=', $tag->id)
          ->exists();

        if ($exists) {
          throw new ConflictException('Tag with this slug already exists');
        }
      }

      $tag->update($data);
      return $tag->fresh();
    });
  }

  public function deleteTag($id): void
  {
    throw_if(
      !Auth::check(),
      ForbiddenException::class,
      'Only administrators can delete tags'
    );

    $tag = Tag::find($id);
    throw_if(!$tag, NotFoundException::class, 'Tag not found');

    $tag->delete();
  }

  public function getPopularTags(int $limit = 10)
  {
    return Tag::withCount('articles')
      ->orderBy('articles_count', 'desc')
      ->limit($limit)
      ->get();
  }
}
