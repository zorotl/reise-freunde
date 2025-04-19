<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Martin Striednig',
            'email' => 'info@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory()->create([
            'name' => 'Zorotl von Zorot',
            'email' => 'zorotl@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory(8)->create();

        $this->call(UserGrantSeeder::class);
        $this->call(UserAdditionalInfoSeeder::class);

        // Seed User Additional Info for all users except ID 1
        User::whereNotIn('id', [1])->get()->each(function (User $user) {
            \Database\Factories\UserAdditionalInfoFactory::new()->create(['user_id' => $user->id]);
        });

        Post::factory(50)->create();
    }
}
