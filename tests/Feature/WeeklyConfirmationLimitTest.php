<?php

use App\Models\User;
use App\Models\UserConfirmation;
use Illuminate\Support\Facades\Auth;

it('blocks more than 3 confirmation requests per week', function () {
    $sender = User::factory()->create();
    $targets = User::factory()->count(4)->create(); // 4 targets

    $this->actingAs($sender);

    // Send 3 requests within the past 7 days
    foreach ($targets->take(3) as $target) {
        UserConfirmation::create([
            'requester_id' => $sender->id,
            'confirmer_id' => $target->id,
            'created_at' => now()->subDays(2),
            'status' => 'pending',
        ]);
    }

    // Attempt to send a 4th one
    $fourthTarget = $targets->last();

    $response = Livewire::test(\App\Livewire\Profile\ConfirmationRequest::class, [
        'target' => $fourthTarget
    ])->call('sendRequest');

    $this->assertDatabaseMissing('user_confirmations', [
        'requester_id' => $sender->id,
        'confirmer_id' => $fourthTarget->id,
    ]);
});
