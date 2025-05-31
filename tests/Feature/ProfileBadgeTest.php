<?php

use App\Models\User;
use App\Models\UserVerification;
use Livewire\Livewire;

it('shows correct badges for a fully verified user', function () {
    $user = User::factory()->create(['status' => 'approved']);

    UserVerification::create([
        'user_id' => $user->id,
        'id_document_path' => 'verifications/id.png',
        'social_links' => ['https://linkedin.com/in/test'],
        'note' => 'Just testing.',
        'status' => 'pending',
    ]);

    Livewire::test(\App\Livewire\Profile\Badges::class, ['user' => $user])
        ->assertSee('Account Approved')
        ->assertSee('ID Verified')
        ->assertSee('Social Linked');
});
