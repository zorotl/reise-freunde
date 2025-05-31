<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->languageCode(), // 'en', 'de', etc.
            'name_en' => $this->faker->word(),
        ];
    }
}

