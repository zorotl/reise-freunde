<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguageSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TrustSystemSeeder::class);
        $this->call(UserGrantSeeder::class);
        $this->call(UserAdditionalInfoSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(HobbyAndTravelStyleSeeder::class);
        $this->call(MessageSeeder::class);
        $this->call(LargeMessageSeeder::class);
        $this->call(UserFollowerSeeder::class);
        $this->call(PostLikeSeeder::class);
        $this->call(BanHistorySeeder::class);

        // Ensure every user has a UserGrant and UserAdditionalInfo
        \App\Models\User::all()->each(function ($user) {
            if (!$user->grant) {
                \App\Models\UserGrant::factory()->create(['user_id' => $user->id]);
            }
            if (!$user->additionalInfo) {
                \App\Models\UserAdditionalInfo::factory()->create(['user_id' => $user->id]);
            }
        });
    }
}
