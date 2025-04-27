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
        // Add specific info for the first user
        if ($firstUser = User::find(1)) { // Find user with ID 1
            // Use updateOrCreate to avoid errors if the record somehow exists
            UserAdditionalInfo::updateOrCreate(
                ['user_id' => $firstUser->id], // Match condition
                [
                    'username' => 'martin84',
                    'birthday' => '1984-08-28',
                    'nationality' => 'CH', // Use the ISO 3166-1 alpha-2 code for Switzerland
                ]
            );
        }

        User::where('id', '>', 1)->each(function (User $user) {
            if (!$user->additionalInfo) {
                UserAdditionalInfo::factory()->create(['user_id' => $user->id]);
            }
        });
    }
}