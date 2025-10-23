<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->word();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
        ];
    }
}
