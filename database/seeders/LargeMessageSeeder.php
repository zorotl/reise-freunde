<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LargeMessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->count() < 2) {
            $this->command->warn('Not enough users to seed messages. Please seed users first or create at least 2 users.');
            // Optionally, create a couple of users here if none exist
            if ($users->count() === 0) {
                User::factory()->count(2)->create()->each(function ($user) {
                    \App\Models\UserAdditionalInfo::factory()->create(['user_id' => $user->id]);
                    \App\Models\UserGrant::factory()->create(['user_id' => $user->id]);
                });
                $users = User::all();
            } elseif ($users->count() === 1) {
                $anotherUser = User::factory()->create();
                \App\Models\UserAdditionalInfo::factory()->create(['user_id' => $anotherUser->id]);
                \App\Models\UserGrant::factory()->create(['user_id' => $anotherUser->id]);
                $users = User::all();
            }
            if ($users->count() < 2)
                return;
        }

        $user1 = $users->first();
        $user2 = $users->skip(1)->first();
        $numberOfMessages = 100; // Create 100 messages for user1

        $this->command->info("Seeding {$numberOfMessages} messages for user ID {$user1->id} (as sender and receiver)...");

        $batchSize = 500;
        $messagesData = [];

        for ($i = 0; $i < $numberOfMessages; $i++) {
            $isSender = ($i % 2 === 0);
            $messagesData[] = [
                'sender_id' => $isSender ? $user1->id : $user2->id,
                'receiver_id' => $isSender ? $user2->id : $user1->id,
                'subject' => 'Performance Test Message ' . $i,
                'body' => 'This is a test message body for performance analysis. Iteration ' . $i,
                'created_at' => Carbon::now()->subMinutes(rand(1, 60 * 24 * 90)), // Randomly within last 90 days
                'updated_at' => Carbon::now()->subMinutes(rand(1, 60 * 24 * 90)),
                'read_at' => ($isSender || rand(0, 1)) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 30)) : null, // Some unread for receiver
                'sender_deleted_at' => (rand(1, 10) == 1 && $isSender) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 10)) : null, // 10% in sender's trash
                'receiver_deleted_at' => (rand(1, 10) == 1 && !$isSender) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 10)) : null, // 10% in receiver's trash
                'sender_archived_at' => (rand(1, 10) == 2 && $isSender) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 10)) : null,
                'receiver_archived_at' => (rand(1, 10) == 2 && !$isSender) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 10)) : null,
                'sender_permanently_deleted_at' => (rand(1, 20) == 1 && $isSender && isset($messagesData[count($messagesData) - 1]['sender_deleted_at'])) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 5)) : null,
                'receiver_permanently_deleted_at' => (rand(1, 20) == 1 && !$isSender && isset($messagesData[count($messagesData) - 1]['receiver_deleted_at'])) ? Carbon::now()->subMinutes(rand(1, 60 * 24 * 5)) : null,
            ];

            if (count($messagesData) >= $batchSize) {
                Message::insert($messagesData);
                $messagesData = [];
                $this->command->info("Inserted {$batchSize} messages...");
            }
        }
        if (!empty($messagesData)) {
            Message::insert($messagesData); // Insert remaining
            $this->command->info("Inserted " . count($messagesData) . " messages...");
        }
        $this->command->info("Finished seeding messages for user ID {$user1->id}.");
    }
}