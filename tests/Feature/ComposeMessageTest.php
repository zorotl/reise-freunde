<?php

use App\Livewire\Mail\MessageCompose;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows validation error when recipient is missing', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(MessageCompose::class)
        ->set('subject', 'Hello')
        ->set('body', 'Test content')
        ->call('sendMessage')
        ->assertHasErrors(['receiver_id']);
});

it('shows validation error when subject is missing', function () {
    $recipient = User::factory()->create();
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(MessageCompose::class)
        ->set('receiver_id', $recipient->id)
        ->set('body', 'Test content')
        ->call('sendMessage')
        ->assertHasErrors(['subject']);
});

it('shows empty subject and body when composing new message (not a reply)', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(MessageCompose::class)
        ->assertSet('subject', '')
        ->assertSet('body', '');
});
