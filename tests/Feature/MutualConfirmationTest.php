<?php

use App\Models\User;
use App\Models\UserConfirmation;

it('shows mutual confirmation in both user profiles', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    // Alice sends confirmation to Bob
    UserConfirmation::create([
        'requester_id' => $alice->id,
        'confirmer_id' => $bob->id,
        'status' => 'pending',
    ]);

    // Bob accepts
    $confirmation = UserConfirmation::where('requester_id', $alice->id)->first();
    $confirmation->status = 'accepted';
    $confirmation->save();

    // Check both sides have the connection
    $confirmedForAlice = $alice->confirmedConnections()->pluck('id')->toArray();
    $confirmedForBob = $bob->confirmedConnections()->pluck('id')->toArray();

    expect($confirmedForAlice)->toContain($bob->id);
    expect($confirmedForBob)->toContain($alice->id);
});
