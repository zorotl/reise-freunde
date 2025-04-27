<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Str;
use App\Models\UserAdditionalInfo; // Import if needed for uniqueness check later

trait GeneratesUsername
{
    /**
     * Generate a unique username based on first/last name.
     * Note: The uniqueness check here is basic. Robust checking happens during validation.
     */
    protected function generateDefaultUsername(?string $firstname, ?string $lastname): string
    {
        $base = Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', ($firstname ?: '') . ($lastname ?: 'user')));
        if (empty($base)) {
            $base = 'user'; // Ensure usernameBase is never empty
        }

        // Simple approach for default generation, uniqueness primarily handled by validation rule
        // You could add a basic DB check here if absolutely necessary, but it adds overhead.
        $username = '';
        $attempts = 0;
        $faker = \Faker\Factory::create(); // Create faker instance locally if needed

        do {
            $randomNumber = $faker->numberBetween(100, 9999); // Wider range
            $username = $base . $randomNumber;
            $attempts++;
            // Basic check (optional here, validation is key)
            //$exists = UserAdditionalInfo::where('username', $username)->exists();
        } while ($attempts < 5); // Limit attempts for default generation

        // Fallback if somehow still not unique or base was very common (unlikely here)
        // if ($exists) {
        //    $username = $base . $faker->unique()->randomNumber(5);
        // }

        return $username;
    }
}