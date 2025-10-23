<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $publishedArticles = Article::where('status', 'published')->get();
        $approvedComments = Comment::where('is_approved', true)->get();

        // Like articles - each user likes random articles
        foreach ($users as $user) {
            // Each user likes 0-20 random articles
            $articlesToLike = $publishedArticles->random(rand(0, min(20, $publishedArticles->count())));

            foreach ($articlesToLike as $article) {
                // Using factory here instead of direct creation
                Like::factory()->create([
                    'user_id' => $user->id,
                    'likeable_type' => Article::class,
                    'likeable_id' => $article->id,
                ]);
            }

            // Each user likes 0-10 random comments
            if ($approvedComments->count() > 0) {
                $commentsToLike = $approvedComments->random(rand(0, min(10, $approvedComments->count())));

                foreach ($commentsToLike as $comment) {
                    Like::factory()->create([
                        'user_id' => $user->id,
                        'likeable_type' => Comment::class,
                        'likeable_id' => $comment->id,
                    ]);
                }
            }
        }
    }
}
