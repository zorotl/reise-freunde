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
        // Füge zusätzliche Informationen zum ersten Benutzer hinzu
        if ($firstUser = User::first()) {
            UserAdditionalInfo::factory()->create([
                'user_id' => $firstUser->id,
                'username' => 'mast', // Behalte den spezifischen Username bei
                'birthday' => '1984-08-28', // Behalte das spezifische Geburtsdatum bei
                'nationality' => 'Swiss', // Behalte die spezifische Nationalität bei
                // 'about_me' wird von der Factory generiert
            ]);
        }

        // Erstelle UserAdditionalInfo für alle Benutzer ausser ID 1
        User::where('id', '>', 1)->each(function (User $user) {
            UserAdditionalInfo::factory()->create(['user_id' => $user->id]);
        });
    }
}
