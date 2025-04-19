<?php

namespace Database\Factories;

use App\Models\User;
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
            'about_me' => $faker->paragraph(3),
        ];
    }
}
