<?php

use App\Models\User;
use App\Models\UserConfirmation;

it('creates a pending confirmation request', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice);

    $confirmation = UserConfirmation::create([
        'requester_id' => $alice->id,
        'confirmer_id' => $bob->id,
        'status' => 'pending',
    ]);

    expect($confirmation->status)->toBe('pending');
    expect($confirmation->requester_id)->toBe($alice->id);
    expect($confirmation->confirmer_id)->toBe($bob->id);
});

it('can accept a confirmation request', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $confirmation = UserConfirmation::create([
        'requester_id' => $alice->id,
        'confirmer_id' => $bob->id,
        'status' => 'pending',
    ]);

    $this->actingAs($bob);

    $confirmation->status = 'accepted';
    $confirmation->save();

    expect($confirmation->fresh()->status)->toBe('accepted');
});
