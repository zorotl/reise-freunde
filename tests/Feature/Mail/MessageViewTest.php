<?php

use App\Models\User;
use App\Models\Message;
use function Pest\Laravel\actingAs;

it('shows message view from inbox with inbox back link', function () {
    $sender = createUserWithDetails(['additionalInfo' => ['username' => 'alice']]);
    $receiver = createUserWithDetails(['additionalInfo' => ['username' => 'bob']]);


    $message = Message::factory()->create([
        'receiver_id' => $receiver->id,
        'sender_id' => $sender->id,
        'subject' => 'Hello from inbox',
        'body' => 'This is a test message.',
    ]);

    actingAs($receiver)
        ->get(route('mail.messages.view', [$message, 'fromWhere' => 'inbox']))
        ->assertOk()
        ->assertSee('Back to Inbox')
        ->assertSee('Hello from inbox')
        ->assertSee('This is a test message.');
});

it('shows message view from outbox with outbox back link', function () {
    $sender = createUserWithDetails(['additionalInfo' => ['username' => 'alice']]);
    $receiver = createUserWithDetails(['additionalInfo' => ['username' => 'bob']]);


    $message = Message::factory()->create([
        'receiver_id' => $receiver->id,
        'sender_id' => $sender->id,
        'subject' => 'Hello from outbox',
        'body' => 'Another test message.',
    ]);

    actingAs($sender)
        ->get(route('mail.messages.view', [$message, 'fromWhere' => 'outbox']))
        ->assertOk()
        ->assertSee('Back to Outbox')
        ->assertSee('Hello from outbox')
        ->assertSee('Another test message.');
});
