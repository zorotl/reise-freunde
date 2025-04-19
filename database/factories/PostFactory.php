<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random existing user ID
        $existingUser = User::inRandomOrder()->first();

        return [
            "user_id" => $existingUser ? $existingUser->id : User::factory()->create()->id,
            "title" => $this->faker->sentence(),
            "content" => $this->faker->paragraph(),
            "expiry_date" => $this->faker->dateTimeBetween('now', '+1 year'),
            "is_active" => $this->faker->boolean(70), // 70% chance to be true
        ];
    }
}
