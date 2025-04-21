<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\TravelStyle;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HobbyAndTravelStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travelStyles = ['Adventure', 'Mountains', 'Camping', 'City Trips', 'Boat/Ship', 'High Mountains', 'Car'];
        $hobbies = ['Photography', 'Hiking', 'Cooking', 'Tennis', 'Cycling', 'Skiing', 'Wellness', 'Wandering', 'Waterski'];

        foreach ($travelStyles as $style) {
            TravelStyle::create(['name' => $style]);
        }

        foreach ($hobbies as $hobby) {
            Hobby::create(['name' => $hobby]);
        }
    }
}
