<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    public function definition(): array
    {
        $likableType = fake()->randomElement([Article::class, Comment::class]);
        $likableId = $likableType === Article::class
            ? Article::factory()->create()->id
            : Comment::factory()->create()->id;

        return [
            'likable_id' => $likableId,
            'likable_type' => $likableType,
            'user_id' => User::factory(),
        ];
    }
}
