<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $articles = Article::where('status', 'published')->get();

        if ($users->isEmpty() || $articles->isEmpty()) {
            $this->command->error('❌ Please run UserSeeder and ArticleSeeder first');
            return;
        }

        $commentTemplates = [
            'Great article! Really helpful information.',
            'Thanks for sharing this. I learned a lot from your insights.',
            'This is exactly what I was looking for. Bookmarked!',
            'Excellent explanation. Very clear and easy to follow.',
            'I have a question about this approach. Could you elaborate more?',
            'Well written! Looking forward to more content like this.',
            'This helped me solve a problem I was stuck on. Thank you!',
            'Interesting perspective. I hadn\'t thought about it this way.',
            'Could you provide more examples on this topic?',
            'Really appreciate the detailed breakdown. Very useful!',
            'This is a comprehensive guide. Great work!',
            'I tried this and it works perfectly. Thanks!',
            'Do you have any recommendations for further reading?',
            'Love the practical examples you included.',
            'This clarified a lot of confusion I had. Thank you!',
        ];

        $replyTemplates = [
            'Thank you for your feedback! Glad you found it helpful.',
            'Great question! Let me explain further...',
            'I appreciate your comment. You make a good point.',
            'Thanks for reading! I\'ll consider covering that in a future post.',
            'You\'re welcome! Feel free to reach out if you have more questions.',
            'That\'s a great suggestion. I\'ll look into that.',
            'I\'m glad it helped! Let me know if you need any clarification.',
            'Absolutely! I\'ll add more examples in an update.',
            'Thank you! More content coming soon.',
            'Good point! I should have mentioned that.',
        ];

        // Create parent comments for random articles
        foreach ($articles->random(min(8, $articles->count())) as $article) {
            $numComments = rand(2, 5);

            for ($i = 0; $i < $numComments; $i++) {
                $parentComment = Comment::create([
                    'content' => $commentTemplates[array_rand($commentTemplates)],
                    'is_approved' => rand(0, 10) > 1, // 90% approved
                    'parent_id' => null,
                    'article_id' => $article->id,
                    'user_id' => $users->random()->id,
                    'created_at' => now()->subDays(rand(1, 20)),
                ]);

                // Add replies to some comments (40% chance)
                if (rand(1, 10) <= 4) {
                    $numReplies = rand(1, 3);

                    for ($j = 0; $j < $numReplies; $j++) {
                        Comment::create([
                            'content' => $replyTemplates[array_rand($replyTemplates)],
                            'is_approved' => true,
                            'parent_id' => $parentComment->id,
                            'article_id' => $article->id,
                            'user_id' => $users->random()->id,
                            'created_at' => $parentComment->created_at->addHours(rand(1, 48)),
                        ]);
                    }
                }
            }
        }

        $this->command->info('✅ Comments created successfully');
    }
}
