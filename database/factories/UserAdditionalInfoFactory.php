<?php

namespace Database\Factories;

use App\Models\UserAdditionalInfo;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAdditionalInfo>
 */
class UserAdditionalInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create();

        return [
            'username' => $faker->unique()->userName(),
            'birthday' => $faker->date(),
            'nationality' => $faker->country(),
            'profile_picture' => $faker->optional()->imageUrl(), // Optionales Bild
            'about_me' => $faker->paragraph(3),
            'is_private' => $faker->boolean(30), // 30% Chance auf privates Profil
            'custom_travel_styles' => $faker->optional()->randomElements(['Adventure', 'Relaxing', 'Cultural'], $faker->numberBetween(0, 3)),
            'custom_hobbies' => $faker->optional()->randomElements(['Reading', 'Gaming', 'Sports'], $faker->numberBetween(0, 3)),
        ];
    }

    /**
     * Indicate that the user's additional info is for a private profile.
     */
    public function private(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_private' => true,
        ]);
    }

    /**
     * Indicate that the user's additional info is for a public profile.
     */
    public function public(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_private' => false,
        ]);
    }
}