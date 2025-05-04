<?php
// database/seeders/PostLikeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB; // Import DB facade

class PostLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and posts
        $users = User::all();
        $posts = Post::all();

        if ($users->isEmpty() || $posts->isEmpty()) {
            $this->command->info('Cannot seed post likes: No users or posts found.');
            return;
        }

        $likes = [];
        $likeCount = 0;
        $maxLikesToSeed = $users->count() * $posts->count() * 0.3; // Seed about 30% possible likes

        // Iterate through posts and randomly assign likes from users
        foreach ($posts as $post) {
            // Select a random number of users to like this post (e.g., 0 to 5 users)
            $likers = $users->random(min($users->count(), rand(0, 5)));

            foreach ($likers as $user) {
                // Ensure a user doesn't like their own post
                if ($post->user_id === $user->id) {
                    continue;
                }

                // Prepare data for bulk insertion
                // Check uniqueness combination to avoid DB errors if loop generates duplicates
                $uniqueKey = $user->id . '-' . $post->id;
                if (!isset($likes[$uniqueKey])) {
                    $likes[$uniqueKey] = [
                        'user_id' => $user->id,
                        'post_id' => $post->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $likeCount++;
                    if ($likeCount >= $maxLikesToSeed)
                        break 2; // Stop seeding if we reach the limit
                }
            }
        }

        // Bulk insert the likes for better performance
        if (!empty($likes)) {
            // Use DB::table for direct insertion into the pivot table
            DB::table('post_likes')->insert(array_values($likes));
            $this->command->info(count($likes) . ' post likes seeded.');
        } else {
            $this->command->info('No post likes were seeded.');
        }
    }
}