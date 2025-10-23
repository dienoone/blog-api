<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Article;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\BadRequestException;
use Auth;
use Illuminate\Support\Facades\DB;

class CommentService
{
  public function getArticleComments($articleId, array $filters = [])
  {
    $article = Article::find($articleId);
    throw_if(!$article, NotFoundException::class, 'Article not found');

    $query = $article->comments()
      ->with(['user', 'replies.user'])
      ->whereNull('parent_id');

    // Order by
    $orderBy = $filters['order_by'] ?? 'created_at';
    $orderDirection = $filters['order_direction'] ?? 'desc';
    $query->orderBy($orderBy, $orderDirection);

    return $query->get();
  }

  public function createComment(array $data): Comment
  {
    $article = Article::find($data['article_id']);
    throw_if(!$article, NotFoundException::class, 'Article not found');


    throw_if(
      !$article->isPublished(),
      BadRequestException::class,
      'Comments are not allowed on unpublished articles'
    );

    // Check parent comment exists if replying
    if (!empty($data['parent_id'])) {
      $parent = Comment::find($data['parent_id']);
      throw_if(!$parent, NotFoundException::class, 'Parent comment not found');

      // Ensure parent belongs to same article
      throw_if(
        $parent->article_id !== $article->id,
        BadRequestException::class,
        'Parent comment does not belong to this article'
      );
    }

    return DB::transaction(function () use ($data) {
      $data['user_id'] = Auth::id();
      $data['is_approved'] = false; // Auto-approve admin comments

      return Comment::create($data)->load('user');
    });
  }

  public function updateComment($id, array $data): Comment
  {
    $comment = Comment::find($id);
    throw_if(!$comment, NotFoundException::class, 'Comment not found');

    throw_if(
      Auth::id() !== $comment->user_id,
      ForbiddenException::class,
      'You cannot update this comment'
    );

    $comment->update($data);
    return $comment->fresh('user');
  }

  public function deleteComment($id): void
  {
    $comment = Comment::find($id);
    throw_if(!$comment, NotFoundException::class, 'Comment not found');

    // Check permissions
    throw_if(
      Auth::id() !== $comment->user_id,
      ForbiddenException::class,
      'You cannot delete this comment'
    );

    $comment->delete();
  }
}
