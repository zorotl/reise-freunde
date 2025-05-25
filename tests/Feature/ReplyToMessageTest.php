<?php

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prefills subject and body when replying to a message', function () {
    // Arrange
    $sender = User::factory()->create(['firstname' => 'Alice', 'lastname' => 'Muster']);
    $recipient = User::factory()->create([
        'email_verified_at' => now(),
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $recipient->id,
        'subject' => 'Meeting',
        'body' => "Hey there,\nAre you available tomorrow?",
        'created_at' => now()->subDay(),
    ]);

    // Act
    $this->actingAs($recipient)
        ->get(route('mail.compose', [
            'receiverId' => $sender->id,
            'fixReceiver' => true,
            'replyToId' => $message->id,
        ]))
        ->assertSee('Re: Meeting')
        ->assertSee("On " . $message->created_at->format('Y-m-d H:i') . ", {$sender->name} wrote:")
        ->assertSee('> Hey there,')
        ->assertSee('> Are you available tomorrow?');
});
