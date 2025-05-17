<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAdditionalInfo;
use App\Models\UserVerification;
use App\Models\UserConfirmation;

class TrustSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 users with all info
        User::factory()
            ->count(3)
            ->hasAdditionalInfo()
            ->hasVerification()->state(['status' => 'approved'])
            ->create();

        // Create extra users with different statuses
        $verified = User::factory()->create(['status' => 'approved']);
        $auto = User::factory()->create(['status' => 'approved']);
        UserAdditionalInfo::factory()->create(['user_id' => $verified->id]);
        UserAdditionalInfo::factory()->create(['user_id' => $auto->id]);

        UserVerification::factory()->create([
            'user_id' => $verified->id,
            'status' => 'reviewed',
        ]);

        // Create 10 Bürgschaften (RealWorldConfirmations)
        UserConfirmation::factory()
            ->count(5)
            ->create();
    }
}

