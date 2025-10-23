<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        // Handle featured_image - check if it's already a full URL
        $featuredImage = null;
        if ($this->featured_image) {
            if (str_starts_with($this->featured_image, 'http')) {
                // Already a full URL from faker
                $featuredImage = $this->featured_image;
            } else {
                // Relative path - prepend storage
                $featuredImage = asset("storage/{$this->featured_image}");
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->route()->getName() === 'articles.show', $this->content),
            'featured_image' => $featuredImage,
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'views_count' => $this->views_count,
            'comments_count' => $this->whenCounted('comments'),
            'author' => new UserResource($this->whenLoaded('author')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            // 'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
