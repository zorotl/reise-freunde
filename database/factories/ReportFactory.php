<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),                 // ✅ Who reported
            'reportable_id' => Post::factory(),               // ✅ Could be User, Message, etc.
            'reportable_type' => Post::class,                 // ✅ Polymorphic type
            'reason' => fake()->randomElement(['spam', 'harassment', 'nudity', 'scam']), // ✅ predefined reason
            'comment' => fake()->optional()->sentence(),      // ✅ Optional additional info
            'status' => 'pending',
            'processed_by' => null,
            'processed_at' => null,
        ];
    }
}


