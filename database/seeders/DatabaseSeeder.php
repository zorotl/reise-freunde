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
        $this->call(UserSeeder::class);
        $this->call(UserGrantSeeder::class);
        $this->call(UserAdditionalInfoSeeder::class);
        $this->call(HobbyAndTravelStyleSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(MessageSeeder::class); // Neuer Seeder
        $this->call(UserFollowerSeeder::class); // Neuer Seeder
    }
}
