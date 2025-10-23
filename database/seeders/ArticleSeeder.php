<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->error('❌ Please run UserSeeder and CategorySeeder first');
            return;
        }

        $articles = [
            [
                'title' => 'Getting Started with Laravel 11',
                'excerpt' => 'Learn the basics of Laravel 11 and build your first web application',
                'content' => 'Laravel 11 brings exciting new features and improvements. In this comprehensive guide, we\'ll explore the fundamentals of Laravel development, from setting up your environment to creating your first routes and controllers. Laravel is a powerful PHP framework that makes web development enjoyable and efficient.',
                'status' => 'published',
                'published_at' => now()->subDays(30),
                'views_count' => 1250,
                'featured_image' => 'articles/laravel-11.jpg',
            ],
            [
                'title' => 'Top 10 Travel Destinations for 2025',
                'excerpt' => 'Discover the most exciting places to visit this year',
                'content' => 'From pristine beaches to bustling cities, explore our handpicked selection of the best travel destinations for 2025. Whether you\'re seeking adventure, relaxation, or cultural experiences, these destinations offer something special for every traveler. Let\'s dive into the world\'s most beautiful locations.',
                'status' => 'published',
                'published_at' => now()->subDays(25),
                'views_count' => 980,
                'featured_image' => 'articles/travel-2025.jpg',
            ],
            [
                'title' => 'Mastering Vue.js 3 Composition API',
                'excerpt' => 'A deep dive into Vue.js 3\'s powerful Composition API',
                'content' => 'The Composition API in Vue.js 3 revolutionizes how we build components. This tutorial will guide you through reactive references, computed properties, and lifecycle hooks. We\'ll build practical examples that demonstrate the power and flexibility of this modern approach to Vue development.',
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'views_count' => 2100,
                'featured_image' => 'articles/vuejs-composition.jpg',
            ],
            [
                'title' => '10 Healthy Breakfast Recipes to Start Your Day',
                'excerpt' => 'Nutritious and delicious breakfast ideas for busy mornings',
                'content' => 'Start your day right with these wholesome breakfast recipes. From protein-packed smoothie bowls to savory egg dishes, these recipes are designed to fuel your body and satisfy your taste buds. Each recipe includes nutritional information and preparation tips.',
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'views_count' => 750,
                'featured_image' => 'articles/breakfast-recipes.jpg',
            ],
            [
                'title' => 'Docker Best Practices for Development',
                'excerpt' => 'Optimize your Docker workflow with these proven techniques',
                'content' => 'Docker has transformed how we develop and deploy applications. This guide covers essential Docker best practices, from writing efficient Dockerfiles to managing multi-container applications with Docker Compose. Learn how to optimize your articles, manage secrets securely, and streamline your development workflow.',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'views_count' => 1450,
                'featured_image' => 'articles/docker-practices.jpg',
            ],
            [
                'title' => 'Understanding Machine Learning Basics',
                'excerpt' => 'An introduction to machine learning concepts and algorithms',
                'content' => 'Machine learning is transforming industries worldwide. This beginner-friendly guide introduces fundamental concepts like supervised learning, unsupervised learning, and neural networks. We\'ll explore real-world applications and help you understand when and how to apply ML techniques.',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'views_count' => 1820,
                'featured_image' => 'articles/ml-basics.jpg',
            ],
            [
                'title' => 'Building RESTful APIs with Laravel',
                'excerpt' => 'Create robust and scalable APIs using Laravel',
                'content' => 'Learn how to build professional RESTful APIs with Laravel. This comprehensive tutorial covers resource controllers, API authentication with Sanctum, rate limiting, and API versioning. We\'ll also discuss best practices for error handling and response formatting.',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'views_count' => 1670,
                'featured_image' => 'articles/laravel-api.jpg',
            ],
            [
                'title' => 'The Ultimate Guide to Remote Work',
                'excerpt' => 'Tips and tools for successful remote work experience',
                'content' => 'Remote work is here to stay. This guide provides actionable advice for staying productive, maintaining work-life balance, and building effective communication habits while working from home. Discover the tools and techniques that successful remote workers use daily.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'views_count' => 920,
                'featured_image' => 'articles/remote-work.jpg',
            ],
            [
                'title' => 'CSS Grid vs Flexbox: When to Use What',
                'excerpt' => 'Understanding the differences and use cases for modern CSS layouts',
                'content' => 'CSS Grid and Flexbox are powerful layout tools, but knowing when to use each is crucial. This article breaks down the strengths of each system, provides practical examples, and helps you choose the right tool for your layout needs.',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'views_count' => 1340,
                'featured_image' => 'articles/css-layouts.jpg',
            ],
            [
                'title' => 'Introduction to Test-Driven Development',
                'excerpt' => 'Write better code with TDD methodology',
                'content' => 'Test-Driven Development (TDD) improves code quality and design. Learn the red-green-refactor cycle, discover testing frameworks, and understand how TDD leads to more maintainable and reliable applications. Includes practical examples in multiple languages.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'views_count' => 890,
                'featured_image' => 'articles/tdd-intro.jpg',
            ],
            [
                'title' => 'Exploring GraphQL: A Modern API Alternative',
                'excerpt' => 'Why GraphQL is changing how we build APIs',
                'content' => 'GraphQL offers a flexible and efficient approach to API development. This guide introduces GraphQL fundamentals, compares it with REST, and walks through building your first GraphQL server. Learn about queries, mutations, subscriptions, and schema design.',
                'status' => 'draft',
                'published_at' => null,
                'views_count' => 0,
                'featured_image' => 'articles/graphql.jpg',
            ],
            [
                'title' => '5 Fitness Myths Debunked',
                'excerpt' => 'Separating fitness facts from fiction',
                'content' => 'The fitness industry is full of misconceptions. We\'re breaking down the most common fitness myths with science-backed evidence. Learn the truth about cardio, strength training, nutrition timing, and more to optimize your workout routine.',
                'status' => 'published',
                'published_at' => now()->subDay(),
                'views_count' => 430,
                'featured_image' => 'articles/fitness-myths.jpg',
            ],
        ];

        foreach ($articles as $articleData) {
            $article = Article::firstOrCreate(
                ['title' => $articleData['title']],
                array_merge($articleData, [
                    'slug' => $articleData['title'],
                    'author_id' => $users->random()->id,
                    'category_id' => $categories->random()->id,
                ])
            );

            // Attach random tags (2-5 tags per article)
            if ($article->wasRecentlyCreated && $tags->isNotEmpty()) {
                $article->tags()->attach(
                    $tags->random(rand(2, min(5, $tags->count())))->pluck('id')->toArray()
                );
            }
        }

        $this->command->info('✅ Articles created successfully');
    }
}
