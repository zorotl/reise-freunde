<?php

namespace Database\Factories;

use App\Models\User; // Import the User model
use App\Models\UserAdditionalInfo;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Import Str facade
use Monarobase\CountryList\CountryListFacade; // Import the Facade

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

        // Get a list of valid ISO 3166-1 alpha-2 country codes from the package
        // Wrap in try-catch in case the package isn't fully available during initial setup/testing phases outside full app boot
        try {
            $countryCodes = array_keys(CountryListFacade::getList('en')); // Get codes using English list
            $nationalityCode = !empty($countryCodes) ? $faker->randomElement($countryCodes) : 'US'; // Default to 'US' if list is empty
        } catch (\Exception $e) {
            // Fallback if the facade isn't available (e.g., during composer install before providers are registered)
            $countryCodes = ['US', 'GB', 'DE', 'CH', 'FR', 'CA', 'AU']; // Example fallback codes
            $nationalityCode = $faker->randomElement($countryCodes);
        }


        // Define other attributes first
        return [
            'birthday' => $faker->date(),
            'nationality' => $nationalityCode,
            'profile_picture_path' => null,
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
                // Use a default base if firstname is null or empty
                $usernameBase = Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', $firstname ?: 'user'));
                if (empty($usernameBase)) {
                    $usernameBase = 'user'; // Ensure usernameBase is never empty
                }


                // Generate username and ensure uniqueness (simple retry loop)
                $username = '';
                $attempts = 0;
                do {
                    $randomNumber = $this->faker->numberBetween(1, 999); // Increased range for better uniqueness
                    $username = $usernameBase . $randomNumber;
                    $attempts++;
                    // Check if username already exists, retry up to 10 times
                    $exists = UserAdditionalInfo::where('username', $username)->exists();
                } while ($exists && $attempts < 10);

                // If still not unique after attempts, add more randomness (or handle error)
                if ($exists) {
                    $username = $usernameBase . $this->faker->unique()->randomNumber(4);
                }

                // Ensure username is not null before saving
                if (!is_null($username)) {
                    $additionalInfo->username = $username;
                    $additionalInfo->save(); // Save the updated username
                } else {
                    // Handle the case where a unique username couldn't be generated (log error, assign default, etc.)
                    // For now, let's assign a fallback unique username
                    $additionalInfo->username = $this->faker->unique()->userName() . Str::random(3);
                    $additionalInfo->save();
                }

            } else {
                // Fallback if user relationship isn't available (shouldn't happen with standard factory usage)
                $additionalInfo->username = $this->faker->unique()->userName() . Str::random(3);
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