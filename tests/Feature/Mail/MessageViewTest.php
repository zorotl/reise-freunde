<?php

namespace Tests\Feature\Mail;

use App\Models\User;
use App\Models\UserAdditionalInfo; // Make sure these are used by the helper
use App\Models\UserGrant;         // Make sure these are used by the helper
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr; // For Arr::only in helper
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get; // get() is used for direct route testing

if (!function_exists('Tests\Feature\Mail\createUserWithDetails')) {
    function createUserWithDetails(array $details = []): User
    {
        $userData = Arr::only($details, ['firstname', 'lastname', 'email', 'password', 'email_verified_at', 'approved_at']); // Add approved_at
        if (empty($userData['email'])) {
            $userData['email'] = \Illuminate\Support\Str::random(10) . '@example.com';
        }
        if (empty($userData['email_verified_at'])) { // Ensure verified
            $userData['email_verified_at'] = now();
        }
        if (empty($userData['approved_at'])) { // Ensure approved
            $userData['approved_at'] = now();
        }

        $user = User::factory()->create($userData);

        UserAdditionalInfo::factory()->create(array_merge(
            ['user_id' => $user->id, 'username' => 'testuser' . $user->id . \Illuminate\Support\Str::random(3)],
            $details['additionalInfo'] ?? []
        ));
        // Ensure grant exists and is NOT banned
        UserGrant::factory()->create([
            'user_id' => $user->id,
            'is_banned' => false, // Explicitly not banned
        ]);
        return $user->fresh(['additionalInfo', 'grant']);
    }
}

it('shows message view from inbox with inbox back link', function () {
    $sender = createUserWithDetails(['additionalInfo' => ['username' => 'alice_inbox_sender']]);
    $receiver = createUserWithDetails(['additionalInfo' => ['username' => 'bob_inbox_receiver']]);

    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'subject' => 'Hello from inbox',
        'body' => 'This is a test message.',
        'read_at' => null,
        'sender_deleted_at' => null,
        'receiver_deleted_at' => null,
        'sender_archived_at' => null,
        'receiver_archived_at' => null,
    ]);

    actingAs($receiver)
        // Pass the message model instance directly for route model binding
        ->get(route('mail.messages.view', ['message' => $message, 'fromWhere' => 'inbox']))
        ->assertOk()
        ->assertSee('Back to Inbox')
        ->assertSee('Hello from inbox')
        ->assertSee('This is a test message.');
});

it('shows message view from outbox with outbox back link', function () {
    $sender = createUserWithDetails(['additionalInfo' => ['username' => 'carol_outbox_sender']]);
    $receiver = createUserWithDetails(['additionalInfo' => ['username' => 'dave_outbox_receiver']]);

    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'subject' => 'Hello from outbox',
        'body' => 'Another test message.',
        'read_at' => null,
        'sender_deleted_at' => null,
        'receiver_deleted_at' => null,
        'sender_archived_at' => null,
        'receiver_archived_at' => null,
    ]);

    actingAs($sender)
        // Pass the message model instance
        ->get(route('mail.messages.view', ['message' => $message, 'fromWhere' => 'outbox']))
        ->assertOk()
        ->assertSee('Back to Outbox')
        ->assertSee('Hello from outbox')
        ->assertSee('Another test message.');
});