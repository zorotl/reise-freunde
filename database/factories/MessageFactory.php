<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender = User::inRandomOrder()->first() ?? User::factory()->create();
        $receiver = User::where('id', '!=', $sender->id)->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'read_at' => $this->faker->optional()->dateTime(),
        ];
    }

    /**
     * Indicate that the message has been read.
     */
    public function read(): static
    {
        return $this->state(fn(array $attributes) => [
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn(array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the message is between specific users.
     */
    public function betweenUsers(int $senderId, int $receiverId): static
    {
        return $this->state(fn(array $attributes) => [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
        ]);
    }
}
