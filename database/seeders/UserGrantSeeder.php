<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGrant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserGrantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get User with ID 1
        $user = User::find(1);
        if ($user) {
            // Create a UserGrant entry for the user
            UserGrant::create([
                'user_id' => $user->id,
                'is_admin' => true,
                'is_moderator' => false, // You can set other defaults as needed
            ]);
        }

        $user = User::find(2);
        if ($user) {
            // Create a UserGrant entry for the user
            UserGrant::create([
                'user_id' => $user->id,
                'is_admin' => false,
                'is_moderator' => true, // You can set other defaults as needed
            ]);
        }

        $user = '';
        // Create UserGrant entries for all other users
        foreach (User::where('id', '>', 2)->get() as $user) {
            UserGrant::create([
                'user_id' => $user->id,
                'is_admin' => false,
                'is_moderator' => false, // You can set other defaults as needed
            ]);
        }
    }
}
