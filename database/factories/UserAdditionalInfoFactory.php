<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Faker\Factory as Faker; // Import Faker
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Monarobase\CountryList\CountryListFacade as Countries;

class UserAdditionalInfoFactory extends Factory
{
    protected $model = UserAdditionalInfo::class; // Explicitly define the model

    public function definition(): array
    {
        $faker = Faker::create(); // Use local Faker instance

        try {
            $countryCodes = array_keys(Countries::getList('en'));
            $nationalityCode = !empty($countryCodes) ? $faker->randomElement($countryCodes) : 'US';
        } catch (\Exception $e) {
            $countryCodes = ['US', 'GB', 'DE', 'CH', 'FR', 'CA', 'AU'];
            $nationalityCode = $faker->randomElement($countryCodes);
        }

        return [
            // 'username' will be handled by the configure method or passed explicitly
            'birthday' => $faker->date(),
            'nationality' => $nationalityCode,
            'profile_picture_path' => null,
            'about_me' => $faker->paragraph(3),
            'is_private' => $faker->boolean(30),
            // 'custom_travel_styles' => $faker->optional()->randomElements(['Adventure', 'Relaxing', 'Cultural'], $faker->numberBetween(0, 3)),
            // 'custom_hobbies' => $faker->optional()->randomElements(['Reading', 'Gaming', 'Sports'], $faker->numberBetween(0, 3)),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (UserAdditionalInfo $additionalInfo) {
            // Only generate a username IF ONE IS NOT ALREADY SET and the user relationship exists.
            if ($additionalInfo->user && empty($additionalInfo->username)) {
                $firstname = $additionalInfo->user->firstname;
                $usernameBase = Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', $firstname ?: 'user'));
                if (empty($usernameBase)) {
                    $usernameBase = 'user';
                }

                $username = '';
                $attempts = 0;
                $faker = \Faker\Factory::create(); // Local faker instance

                do {
                    $randomNumber = $faker->numberBetween(100, 9999); // Wider range
                    $username = $usernameBase . $randomNumber;
                    $attempts++;
                    // Check if username already exists, excluding the current record
                    $exists = UserAdditionalInfo::where('username', $username)
                        ->where('id', '!=', $additionalInfo->id)
                        ->exists();
                } while ($exists && $attempts < 20); // Increased attempts

                if ($exists) { // If still not unique, append more random chars
                    $username = $usernameBase . $faker->unique()->numerify('#####') . Str::random(2);
                }

                $additionalInfo->username = $username;
                $additionalInfo->saveQuietly(); // Use saveQuietly to avoid re-triggering events if any
            }
        });
    }

    public function private(): static
    {
        return $this->state(fn(array $attributes) => ['is_private' => true]);
    }

    public function public(): static
    {
        return $this->state(fn(array $attributes) => ['is_private' => false]);
    }
}