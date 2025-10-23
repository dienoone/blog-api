<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'is_approved' => fake()->boolean(80), // 80% approved
            'parent_id' => null,
            'article_id' => Article::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function reply(): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => \App\Models\Comment::factory(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => true,
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => false,
        ]);
    }
}
