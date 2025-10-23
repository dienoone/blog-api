<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Likeable
{
  /**
   * Get all likes for this model
   */
  public function likes(): MorphMany
  {
    return $this->morphMany(Like::class, 'likeable');
  }

  /**
   * Like the model
   */
  public function like(?User $user): void
  {
    $user = $user ?: Auth::user();

    if (!$user) {
      return;
    }

    // Check if already liked
    if ($this->isLikedBy($user)) {
      return;
    }

    $this->likes()->create([
      'user_id' => $user->id,
    ]);
  }

  /**
   * Unlike the model
   */
  public function unlike(?User $user): void
  {
    $user = $user ?: Auth::user();

    if (!$user) {
      return;
    }

    $this->likes()
      ->where('user_id', $user->id)
      ->delete();
  }

  /**
   * Toggle like status
   */
  public function toggleLike(?User $user = null): void
  {
    $user = $user ?: Auth::user();

    if ($this->isLikedBy($user)) {
      $this->unlike($user);
    } else {
      $this->like($user);
    }
  }

  /**
   * Check if the model is liked by a user
   */
  public function isLikedBy(?User $user = null): bool
  {
    $user = $user ?: Auth::user();

    if (!$user) {
      return false;
    }

    return $this->likes()
      ->where('user_id', $user->id)
      ->exists();
  }

  /**
   * Get the number of likes
   */
  public function likesCount(): int
  {
    return $this->likes()->count();
  }

  /**
   * Get users who liked this model
   */
  public function likers()
  {
    return User::whereHas('likes', function ($query) {
      $query->where('likeable_id', $this->id)
        ->where('likeable_type', get_class($this));
    });
  }

  /**
   * Scope to order by likes count
   */
  public function scopePopular($query)
  {
    return $query->withCount('likes')
      ->orderBy('likes_count', 'desc');
  }

  /**
   * Scope to get only liked items by a user
   */
  public function scopeLikedBy($query, User $user)
  {
    return $query->whereHas('likes', function ($query) use ($user) {
      $query->where('user_id', $user->id);
    });
  }
}
