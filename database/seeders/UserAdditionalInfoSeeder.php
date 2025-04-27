<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Illuminate\Database\Seeder;

class UserAdditionalInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Füge spezifischen Informationen zum ersten Benutzer hinzu
        if ($firstUser = User::first()) {
            UserAdditionalInfo::factory()->create([
                'user_id' => $firstUser->id,
                'username' => 'mast',
                'birthday' => '1984-08-28',
                'nationality' => 'Switzerland',
            ]);
        }

        // Erstelle UserAdditionalInfo für alle Benutzer ausser ID 1
        User::where('id', '>', 1)->each(function (User $user) {
            UserAdditionalInfo::factory()->create(['user_id' => $user->id]);
        });
    }
}
