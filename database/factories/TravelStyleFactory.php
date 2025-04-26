<?php

namespace Database\Factories;

use App\Models\TravelStyle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelStyleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TravelStyle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(), // Generate a unique random word for the travel style name
        ];
    }
}