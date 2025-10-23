<?php

namespace App\Models;

use App\Traits\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'views_count',
        'author_id',
        'category_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views_count' => 'integer'
    ];

    // protected $withCount = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (!$article->slug) {
                $article->slug = Str::slug($article->title);

                // Ensure unique slug
                $originalSlug = $article->slug;
                $count = 1;
                while (static::where('slug', $article->slug)->exists()) {
                    $article->slug = $originalSlug . '-' . $count++;
                }
            }
        });


        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);


                // Ensure unique slug
                if ($article->isDirty('slug')) { // ADDED: check if slug changed
                    $originalSlug = $article->slug;
                    $count = 1;
                    while (static::where('slug', $article->slug)
                        ->where('id', '!=', $article->id) // ADDED: exclude current article
                        ->exists()
                    ) {
                        $article->slug = $originalSlug . '-' . $count++;
                    }
                }
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
