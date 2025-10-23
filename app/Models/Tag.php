<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    protected $withCount = ['articles'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }

            if ($tag->isDirty('slug')) {
                $originalSlug = $tag->slug;
                $count = 1;
                while (static::where('slug', $tag->slug)
                    ->where('id', '!=', $tag->id)
                    ->exists()
                ) {
                    $tag->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }
}
