<?php

namespace Database\Factories;

use App\Models\BanHistory;
use App\Models\User; // Import User model
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BanHistory>
 */
class BanHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Find an existing user to be banned, or create one
        $bannedUser = User::inRandomOrder()->first() ?? User::factory()->create();

        // Find an existing admin/mod to be the banner, or create one
        // Ensure the banner is not the same as the banned user
        $banner = User::whereHas('grant', fn($q) => $q->where('is_admin', true)->orWhere('is_moderator', true))
            ->where('id', '!=', $bannedUser->id) // Ensure banner is not the banned user
            ->inRandomOrder()
            ->first();

        // If no suitable banner exists, create an admin user (ensure not same as banned user)
        if (!$banner) {
            do {
                $banner = User::factory()->create();
                // Ensure the created user has an admin grant
                $banner->grant()->updateOrCreate(['user_id' => $banner->id], ['is_admin' => true]);
            } while ($banner->id === $bannedUser->id);
        }


        $isTemporaryBan = $this->faker->boolean(70); // 70% chance of temporary ban

        return [
            'user_id' => $bannedUser->id,
            'banned_by' => $banner->id,
            'reason' => $this->faker->sentence(),
            'banned_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'expires_at' => $isTemporaryBan ? $this->faker->dateTimeBetween('now', '+1 year') : null,
        ];
    }

    // Optional state for a permanent ban
    public function permanent(): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => null,
        ]);
    }

    // Optional state for a temporary ban
    public function temporary($expiry = '+6 months'): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('now', $expiry),
        ]);
    }
}