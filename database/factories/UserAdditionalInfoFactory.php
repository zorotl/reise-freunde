<?php

namespace Database\Factories;

use App\Models\User; // Import the User model
use App\Models\UserAdditionalInfo;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Import Str facade

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

        // Define other attributes first
        return [
            // 'username' will be set in the configure method
            'birthday' => $faker->date(),
            'nationality' => $faker->country(),
            'profile_picture' => $faker->optional()->imageUrl(),
            'about_me' => $faker->paragraph(3),
            'is_private' => $faker->boolean(30),
            'custom_travel_styles' => $faker->optional()->randomElements(['Adventure', 'Relaxing', 'Cultural'], $faker->numberBetween(0, 3)),
            'custom_hobbies' => $faker->optional()->randomElements(['Reading', 'Gaming', 'Sports'], $faker->numberBetween(0, 3)),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (UserAdditionalInfo $additionalInfo) {
            // This runs after the model instance is made but before saving
            // If user_id is not set yet (e.g., using User::factory()->has(UserAdditionalInfo::factory())->create()),
            // we might need to use afterCreating instead. Let's try afterMaking first.
        })->afterCreating(function (UserAdditionalInfo $additionalInfo) {
            // This runs after the model instance is created and saved
            if ($additionalInfo->user) { // Check if the user relationship exists
                $firstname = $additionalInfo->user->firstname;
                $usernameBase = Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', $firstname)); // Clean the firstname

                // Generate username and ensure uniqueness (simple retry loop)
                $username = '';
                $attempts = 0;
                do {
                    $randomNumber = $this->faker->numberBetween(1, 99);
                    $username = $usernameBase . $randomNumber;
                    $attempts++;
                    // Check if username already exists, retry up to 10 times
                    $exists = UserAdditionalInfo::where('username', $username)->exists();
                } while ($exists && $attempts < 10);

                // If still not unique after attempts, add more randomness (or handle error)
                if ($exists) {
                    $username = $usernameBase . $this->faker->unique()->randomNumber(4);
                }

                $additionalInfo->username = $username;
                $additionalInfo->save(); // Save the updated username
            } else {
                // Fallback if user relationship isn't available (shouldn't happen with standard factory usage)
                $additionalInfo->username = $this->faker->unique()->userName();
                $additionalInfo->save();
            }
        });
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