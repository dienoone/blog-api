<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Laravel',
            'PHP',
            'JavaScript',
            'Vue.js',
            'React',
            'Python',
            'Docker',
            'AWS',
            'DevOps',
            'Machine Learning',
            'AI',
            'Security',
            'Performance',
            'Tutorial',
            'Tips',
            'Best Practices',
            'Review',
            'News',
            'Update',
            'Guide',
            'How To',
            'Beginner',
            'Advanced',
            'Database',
            'MySQL',
            'PostgreSQL',
            'Redis',
            'MongoDB',
            'API',
            'REST',
            'GraphQL',
            'Testing',
            'TDD',
            'CI/CD'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)]);
        }

        $this->command->info('✅ Tags created successfully');
    }
}
