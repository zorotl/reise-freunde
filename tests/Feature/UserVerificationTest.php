<?php

use App\Models\User;
use Livewire\Livewire;

it('lets a user submit verification info', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(\App\Livewire\Profile\Verify::class)
        ->set('note', 'I am a real person')
        ->set('socialLinks', ['https://linkedin.com/in/realme'])
        ->call('submit');

    expect($user->fresh()->verification()->exists())->toBeTrue();
});