<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\TravelStyle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HobbyAndTravelStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travelStyles = [
            'Adventure',
            'Mountains',
            'Camping',
            'City Trips',
            'Boat/Ship',
            'High Mountains',
            'Car',
            'Backpacking',
            'Luxury Travel',
            'Road Trips',
            'Island Hopping',
            'Cruises',
            'Nature Tours',
            'Safari',
            'Beach Holidays',
            'Cultural Trips',
            'Eco Tourism',
            'Winter Sports',
            'Train Travel',
            'Glamping',
            'Historical Tours',
            'Remote Villages',
            'Wildlife Expeditions'
        ];

        $hobbies = [
            'Photography',
            'Hiking',
            'Cooking',
            'Tennis',
            'Cycling',
            'Skiing',
            'Wellness',
            'Wandering',
            'Waterski',
            'Painting',
            'Fishing',
            'Yoga',
            'Running',
            'Rock Climbing',
            'Diving',
            'Bird Watching',
            'Horseback Riding',
            'Surfing',
            'Golf',
            'Sailing',
            'Pottery',
            'Dancing',
            'Kitesurfing'
        ];

        $travelStyleModels = collect();
        foreach ($travelStyles as $style) {
            $travelStyleModels->push(TravelStyle::create(['name' => $style]));
        }

        $hobbyModels = collect();
        foreach ($hobbies as $hobby) {
            $hobbyModels->push(Hobby::create(['name' => $hobby]));
        }

        // Weise jedem Benutzer zufällig einige Hobbys und Reisestile zu
        User::whereIn('id', range(1, 3))->get()->each(function ($user) use ($hobbyModels, $travelStyleModels) {
            $user->hobbies()->attach($hobbyModels->random(rand(0, 3))->pluck('id'));
            $user->travelStyles()->attach($travelStyleModels->random(rand(0, 3))->pluck('id'));
        });
    }
}
