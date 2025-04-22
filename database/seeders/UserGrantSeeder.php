<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGrant;
use Illuminate\Database\Seeder;

class UserGrantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin-Benutzer (ID 1)
        if ($user = User::find(1)) {
            UserGrant::factory()->admin()->create(['user_id' => $user->id]);
        }

        // Moderator-Benutzer (ID 2)
        if ($user = User::find(2)) {
            UserGrant::factory()->moderator()->create(['user_id' => $user->id]);
        }

        // User Grants für alle anderen Benutzer erstellen
        User::where('id', '>', 2)->each(function (User $user) {
            UserGrant::factory()->create(['user_id' => $user->id]);
        });
    }
}