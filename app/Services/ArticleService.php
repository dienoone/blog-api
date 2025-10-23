<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Article;
use DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleService
{
  public function __construct(protected TagService $tagService) {}

  public function getAllArticles(array $filters = [])
  {
    $query = Article::with(['author', 'category', 'tags', 'likes']);

    // Filter by status
    if (!empty($filters['status'])) {
      if ($filters['status'] === 'published') {
        $query->published();
      } else {
        $query->where('status', $filters['status']);
      }
    }

    // Filter by category
    if (!empty($filters['category'])) {
      $query->where('category_id', $filters['category']);
    }

    // Filter by tag
    if (!empty($filters['tag'])) {
      $query->whereHas('tags', function ($q) use ($filters) {
        $q->where('tags.id', $filters['tag'])
          ->orWhere('tags.slug', $filters['tag']);
      });
    }

    // Filter by author
    if (!empty($filters['author'])) {
      $query->where('author_id', $filters['author']);
    }

    // Search
    if (!empty($filters['search'])) {
      $search = $filters['search'];
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('excerpt', 'like', "%{$search}%")
          ->orWhere('content', 'like', "%{$search}%");
      });
    }

    // Check if liked by current user
    if (Auth::check()) {
      $query->with(['likes' => function ($q) {
        $q->where('user_id', Auth::id());
      }]);
    }

    // Order by
    $orderBy = $filters['order_by'] ?? 'created_at';
    $orderDirection = $filters['order_direction'] ?? 'desc';

    if ($orderBy === 'popular') {
      $query->popular();
    } else {
      $query->orderBy($orderBy, $orderDirection);
    }

    // Pagination
    $perPage = $filters['per_page'] ?? 15;
    return $query->paginate($perPage);
  }

  public function getPopularArticles(int $limit = 10)
  {
    return Article::published()
      ->popular()
      ->with(['author', 'category'])
      ->limit($limit)
      ->get();
  }

  public function getArticle($identifier)
  {
    $query = Article::with(['author', 'category', 'tags', 'comments' => function ($q) {
      $q->approved()->with('user');
    }]);

    $article = is_numeric($identifier)
      ? $query->find($identifier)
      : $query->where('slug', $identifier)->first();

    throw_if(!$article, NotFoundException::class, 'Article not found');

    throw_if(
      $article->status !== 'published' && (!Auth::check() || (Auth::id() !== $article->author_id)),
      ForbiddenException::class,
      'You cannot view this article'
    );

    // Increment views for published articles
    if ($article->isPublished()) {
      $article->incrementViews();
    }

    // Add like status and count
    $article->loadCount(['tags', 'comments', 'likes']);

    if (Auth::check()) {
      $article->is_liked = $article->isLikedBy(Auth::user());
    }

    return $article;
  }

  public function createArticle(array $data)
  {
    throw_if(
      !Auth::check(),
      ForbiddenException::class,
      'You do not have permission to create articles'
    );

    return DB::transaction(function () use ($data) {
      // Handle featured image upload
      if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
        $data['featured_image'] = $this->uploadImage($data['featured_image']);
      }

      // Set author
      $data['author_id'] = Auth::id();

      // Handle publish status
      if (isset($data['status']) && $data['status'] === 'published') {
        $data['published_at'] ??= now();
      }

      // Create article
      $article = Article::create($data);

      // Attach tags
      if (!empty($data['tags'])) {
        $tagIds = $this->tagService->getOrCreateTags($data['tags']);
        $article->tags()->sync($tagIds);
      }

      return $article->load(['author', 'category', 'tags']);
    });
  }

  public function updateArticle($id, array $data): Article
  {
    $article = Article::find($id);
    throw_if(!$article, NotFoundException::class, 'Article not found');

    // Check permissions
    throw_if(
      Auth::id() !== $article->author_id,
      ForbiddenException::class,
      'You cannot update this article'
    );

    return DB::transaction(function () use ($article, $data) {
      // Handle featured image upload
      if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
        // Delete old image
        if ($article->featured_image) {
          Storage::disk('public')->delete($article->featured_image);
        }
        $data['featured_image'] = $this->uploadImage($data['featured_image']);
      }

      // Handle publish status
      if (isset($data['status'])) {
        if ($data['status'] === 'published' && $article->status !== 'published') {
          $data['published_at'] ??= now();
        } elseif ($data['status'] !== 'published') {
          $data['published_at'] = null;
        }
      }

      // Update article
      $article->update($data);

      // Update tags
      if (isset($data['tags'])) {
        $tagIds = $this->tagService->getOrCreateTags($data['tags']);
        $article->tags()->sync($tagIds);
      }

      return $article->fresh(['author', 'category', 'tags']);
    });
  }

  public function deleteArticle($id): void
  {
    $article = Article::find($id);
    throw_if(!$article, NotFoundException::class, 'Article not found');

    // Check permissions
    if (Auth::id() !== $article->author_id && !Auth::user()) {
      throw new ForbiddenException('You cannot delete this article');
    }

    DB::transaction(function () use ($article) {
      // Delete featured image
      if ($article->featured_image) {
        Storage::disk('public')->delete($article->featured_image);
      }

      $article->delete();
    });
  }

  public function getUserArticles($userId, array $filters = [])
  {
    $filters['author'] = $userId;
    return $this->getAllArticles($filters);
  }

  public function toggleLike($id): array
  {
    $article = Article::find($id);
    throw_if(!$article, NotFoundException::class, 'Article not found');

    $article->toggleLike();

    return [
      'is_liked' => $article->isLikedBy(),
      'likes_count' => $article->likes()->count(),
    ];
  }

  protected function uploadImage(UploadedFile $image): string
  {
    $filename = time() . '_' . $image->getClientOriginalName();
    $path = $image->storeAs('articles', $filename, 'public');
    return $path;
  }
}
