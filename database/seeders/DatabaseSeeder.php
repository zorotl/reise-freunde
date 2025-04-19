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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Martin',
            'email' => 'info@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory()->create([
            'name' => 'Zorotl',
            'email' => 'zorotl@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory()->create([
            'name' => 'Barbara',
            'email' => 'barbara@stws.ch',
            'password' => Hash::make('test1234'),
        ]);

        Post::factory(10)->create();
    }
}
