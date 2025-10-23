<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest tech news, reviews, and tutorials'],
            ['name' => 'Travel', 'slug' => 'travel', 'description' => 'Travel guides, tips, and destination reviews'],
            ['name' => 'Food & Cooking', 'slug' => 'food-cooking', 'description' => 'Recipes, restaurant reviews, and cooking tips'],
            ['name' => 'Health & Fitness', 'slug' => 'health-fitness', 'description' => 'Health tips, workout routines, and wellness advice'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Business strategies, entrepreneurship, and market insights'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Fashion trends, style guides, and designer news'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports news, analysis, and athlete profiles'],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'description' => 'Movies, TV shows, music, and celebrity news'],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Learning resources, study tips, and educational content'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Daily life, home improvement, and personal development'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['slug' => $category['slug'], 'description' => $category['description']]
            );
        }

        $this->command->info('✅ Categories created successfully');
    }
}
