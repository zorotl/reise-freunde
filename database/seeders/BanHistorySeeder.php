<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BanHistory; // Import the factory

class BanHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find users who are not admins or moderators and are not currently banned
        $eligibleUsers = User::whereDoesntHave('grant', function ($query) {
            $query->where('is_admin', true)
                ->orWhere('is_moderator', true)
                ->orWhere('is_banned', true); // Exclude already banned
        })
            ->inRandomOrder()
            ->take(5) // Create history for up to 5 users
            ->get();

        if ($eligibleUsers->isEmpty()) {
            $this->command->info('No eligible users found to create ban history for.');
            return;
        }

        // Find an admin user to be the 'banned_by' user
        $adminUser = User::whereHas('grant', fn($q) => $q->where('is_admin', true))
            ->inRandomOrder()
            ->first();

        // Fallback if no admin found (shouldn't happen if UserGrantSeeder ran)
        $bannerId = $adminUser ? $adminUser->id : null;


        foreach ($eligibleUsers as $user) {
            // Create 1 to 3 history entries per eligible user
            $numberOfBans = rand(1, 3);
            for ($i = 0; $i < $numberOfBans; $i++) {
                // Use the factory, overriding user_id and potentially banned_by
                BanHistory::factory()->create([
                    'user_id' => $user->id,
                    'banned_by' => $bannerId, // Assign the admin who 'banned' them
                    // Optional: Make dates sequential if needed
                    'banned_at' => now()->subMonths(rand(1, 12))->subDays(rand(0, 30)), // Ban happened in the past
                    // expires_at will be handled by the factory logic (can be null or future)
                ]);
            }
            //$this->command->info("Created {$numberOfBans} ban history entries for user ID: {$user->id}");
        }
    }
}