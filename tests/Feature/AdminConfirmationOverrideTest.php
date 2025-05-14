<?php

use App\Models\User;
use App\Models\UserConfirmation;

it('admin can approve and reject confirmations manually', function () {
    $admin = User::factory()->create();
    $requester = User::factory()->create();
    $confirmer = User::factory()->create();

    // Create a pending confirmation
    $confirmation = UserConfirmation::create([
        'requester_id' => $requester->id,
        'confirmer_id' => $confirmer->id,
        'status' => 'pending',
    ]);

    $this->actingAs($admin);

    // Simulate Livewire admin component approving it
    \Livewire\Livewire::test(\App\Livewire\Admin\Confirmations\Index::class)
        ->call('approve', $confirmation->id);

    expect($confirmation->fresh()->status)->toBe('accepted');

    // Reset to pending
    $confirmation->update(['status' => 'pending']);

    \Livewire\Livewire::test(\App\Livewire\Admin\Confirmations\Index::class)
        ->call('reject', $confirmation->id);

    expect($confirmation->fresh()->status)->toBe('rejected');
});
