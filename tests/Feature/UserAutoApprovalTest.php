<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('auto-approves pending users after 36 hours', function () {
    $user = User::factory()->create([
        'status' => 'pending',
        'email_verified_at' => now()->subDays(2),
        'created_at' => now()->subDays(2),
    ]);

    Artisan::call('users:auto-approve');

    expect($user->fresh()->status)->toBe('approved');
});
