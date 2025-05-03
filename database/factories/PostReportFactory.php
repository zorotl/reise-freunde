<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\PostReport; // Import PostReport model
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostReport>
 */
class PostReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostReport::class; // Ensure the model is linked

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get existing user/post or create new ones if none exist
        $reporter = User::inRandomOrder()->first() ?? User::factory()->create();
        $post = Post::inRandomOrder()->first() ?? Post::factory()->create();

        return [
            'user_id' => $reporter->id,
            'post_id' => $post->id,
            'reason' => $this->faker->optional()->sentence(), // Optional reason
            'status' => 'pending', // Default status
            'processed_by' => null,
            'processed_at' => null,
            // timestamps are handled automatically
        ];
    }

    /**
     * Indicate that the report is accepted.
     */
    public function accepted(): static
    {
        $admin = User::whereHas('grant', fn($q) => $q->where('is_admin', true) || $q->where('is_moderator', true))
            ->inRandomOrder()
            ->first() ?? User::factory()->create(['is_admin' => true]); // Ensure an admin/mod exists

        return $this->state(fn(array $attributes) => [
            'status' => 'accepted',
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the report is rejected.
     */
    public function rejected(): static
    {
        $admin = User::whereHas('grant', fn($q) => $q->where('is_admin', true) || $q->where('is_moderator', true))
            ->inRandomOrder()
            ->first() ?? User::factory()->create(['is_admin' => true]); // Ensure an admin/mod exists

        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);
    }
}