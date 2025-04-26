<?php

namespace Database\Factories;

use App\Models\UserGrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGrant>
 */
class UserGrantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isBanned = $this->faker->boolean(10); // 10% Chance auf gebannt

        return [
            'is_admin' => $this->faker->boolean(10), // 10% Chance auf Admin
            'is_moderator' => $this->faker->boolean(20), // 20% Chance auf Moderator
            'is_banned' => $isBanned,
            'is_banned_until' => $isBanned ? $this->faker->dateTimeBetween('now', '+1 month') : null,
            'banned_reason' => $isBanned ? 'banned from seeder' : null,
        ];
    }

    /**
     * Indicate that the user grant is for an administrator.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_admin' => true,
            'is_moderator' => false,
            'is_banned' => false,
            'is_banned_until' => null,
        ]);
    }

    /**
     * Indicate that the user grant is for a moderator.
     */
    public function moderator(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_admin' => false,
            'is_moderator' => true,
            'is_banned' => false,
            'is_banned_until' => null,
        ]);
    }

    /**
     * Indicate that the user grant is for a banned user.
     */
    public function banned($until = '+1 year'): static
    {
        return $this->state(fn(array $attributes) => [
            'is_admin' => false,
            'is_moderator' => false,
            'is_banned' => true,
            'is_banned_until' => $this->faker->dateTimeBetween('now', $until),
        ]);
    }
}