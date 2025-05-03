<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Monarobase\CountryList\CountryListFacade as Countries;

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
        // Get a list of valid country codes
        // Wrap in try-catch for safety during potential early testing phases
        try {
            $countryCodes = array_keys(Countries::getList('en'));
            $countryCode = !empty($countryCodes) ? $this->faker->optional(75)->randomElement($countryCodes) : null; // 80% chance of having a country
        } catch (\Exception $e) {
            $countryCodes = ['US', 'GB', 'DE', 'CH', 'FR', 'CA', 'AU', null]; // Example fallback codes + null
            $countryCode = $this->faker->randomElement($countryCodes);
        }

        // Zufälliges Datum für 'from_date' in der Vergangenheit oder Gegenwart
        $fromDate = $this->faker->dateTimeBetween('-1 month', '+6 months');
        // 'to_date' ist immer nach 'from_date'
        $toDate = $this->faker->dateTimeBetween($fromDate, $this->faker->dateTimeInInterval($fromDate, '+1 month'));
        // Zufälliges Ablaufdatum, kann vor oder nach 'to_date' liegen
        $expiryDate = $this->faker->dateTimeBetween('-1 month', $fromDate);
        // Get a random existing user ID
        $existingUser = User::inRandomOrder()->first();

        return [
            "user_id" => $existingUser ? $existingUser->id : User::factory()->create()->id,
            "title" => $this->faker->sentence(),
            "content" => $this->faker->paragraph(),
            "expiry_date" => $expiryDate,
            "is_active" => $this->faker->boolean(75), // 75% chance to be true
            "from_date" => $fromDate,
            "to_date" => $toDate,
            "country" => $countryCode, // Use the generated code
            "city" => $countryCode ? $this->faker->optional(75)->city() : null, // Only add city if country exists
        ];
    }

    /**
     * Indicate that the post is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the post has expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'expiry_date' => $this->faker->dateTimeBetween('-2 weeks', '-1 day'),
        ]);
    }

    /**
     * Indicate that the post is from a specific user ID.
     */
    public function fromUser(int $userId): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}