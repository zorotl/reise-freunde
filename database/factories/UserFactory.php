<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'gender' => Arr::random([
                ...array_fill(0, 47, 'female'),
                ...array_fill(0, 48, 'male'),
                ...array_fill(0, 5, 'diverse'),
            ]),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'status' => fake()->randomElement(['pending', 'approved']),
            'notification_preferences' => [
                'real_world_confirmation' => true,
                'real_world_confirmation_request' => true,
                'verification_reviewed' => true,
                'report_resolved' => true,
            ],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $languages = Language::inRandomOrder()->take(rand(1, 3))->pluck('code');
            $user->spokenLanguages()->attach($languages);
        });
    }
}
