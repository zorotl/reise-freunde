<?php

namespace Database\Factories;

use App\Models\Hobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class HobbyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hobby::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(), // Generate a unique random word for the hobby name
        ];
    }
}