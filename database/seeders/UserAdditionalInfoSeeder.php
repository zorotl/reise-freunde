<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\UserAdditionalInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserAdditionalInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add additional info to the first user
        if ($firstUser = User::first()) {
            UserAdditionalInfo::create([
                'user_id' => $firstUser->id,
                'username' => 'mast',
                'birthday' => '1984-08-28',
                'nationality' => 'Swiss',
                'about_me' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            ]);
        }

        // Seed User Additional Info for all users except ID 1
        User::whereNotIn('id', [1])->get()->each(function (User $user) {
            \Database\Factories\UserAdditionalInfoFactory::new()->create(['user_id' => $user->id]);
        });
    }
}
