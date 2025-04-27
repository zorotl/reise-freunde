<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Livewire\Volt\Volt;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Ensure UserAdditionalInfo exists or is created if needed by the component logic
    $user->additionalInfo()->firstOrCreate(['user_id' => $user->id], ['username' => 'initial_username']);


    $response = Volt::test('settings.profile')
        ->set('firstname', 'Test')
        ->set('lastname', 'User')
        ->set('email', 'test@example.com')
        ->set('username', 'testuser') // Add username update
        ->set('birthday', '1984-08-28')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();
    $user->load('additionalInfo'); // Load relation to check

    expect($user->firstname)->toEqual('Test');
    expect($user->lastname)->toEqual('User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
    expect($user->additionalInfo->birthday->format('Y-m-d'))->toEqual('1984-08-28');
    expect($user->additionalInfo->username)->toEqual('testuser'); // Check updated username
});


test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Ensure UserAdditionalInfo exists or is created if needed by the component logic
    $user->additionalInfo()->firstOrCreate(['user_id' => $user->id], ['username' => 'initial_username']);

    $response = Volt::test('settings.profile')
        ->set('firstname', 'Test')
        ->set('lastname', 'User')
        ->set('email', $user->email) // Keep email same
        ->set('username', 'testuserunchanged') // Add username update
        ->set('birthday', '1984-08-28')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});


test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password') // Assuming default factory password is 'password'
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    // --- FIX: Check if the user is trashed ---
    // expect($user->fresh())->toBeNull(); // This fails with SoftDeletes
    expect($user->fresh()->trashed())->toBeTrue();
    // --- END FIX ---

    expect(auth()->check())->toBeFalse(); // Check user is logged out
});


test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
    expect($user->fresh()->trashed())->toBeFalse(); // Ensure user was NOT soft-deleted
});

test('nationality can be updated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Ensure UserAdditionalInfo exists or is created
    $user->additionalInfo()->firstOrCreate(['user_id' => $user->id], ['username' => 'nationalitytest']);

    $validCountryCode = 'DE'; // Use a valid ISO code for testing

    // --- Assuming settings.profile is a Volt component ---
    $response = Volt::test('settings.profile') // Use the Volt route name/path
        // Set other required fields to prevent validation errors
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        // Ensure username is set, potentially reloading from the created additionalInfo
        ->set('username', $user->additionalInfo()->first()->username)
        ->set('birthday', $user->additionalInfo()->first()->birthday?->format('Y-m-d') ?? '1990-01-01')
        // Set the nationality code
        ->set('nationality', $validCountryCode)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh(); // Refresh user model
    $user->load('additionalInfo'); // Reload the relationship

    // Assert the nationality code was saved
    expect($user->additionalInfo->nationality)->toBe($validCountryCode);

    // --- Test validation for invalid code ---
    $responseInvalidSize = Volt::test('settings.profile')
        ->set('firstname', $user->firstname) // Resend required fields
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', $user->additionalInfo->username)
        ->set('birthday', $user->additionalInfo->birthday?->format('Y-m-d') ?? '1990-01-01')
        ->set('nationality', 'XYZ') // Invalid 3-letter code
        ->call('updateProfileInformation');
    $responseInvalidSize->assertHasErrors(['nationality' => 'size']); // Should fail size validation

    $responseInvalidCode = Volt::test('settings.profile')
        ->set('firstname', $user->firstname) // Resend required fields
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', $user->additionalInfo->username)
        ->set('birthday', $user->additionalInfo->birthday?->format('Y-m-d') ?? '1990-01-01')
        ->set('nationality', 'XX') // Non-existent 2-letter code
        ->call('updateProfileInformation');
    $responseInvalidCode->assertHasErrors(['nationality' => 'in']); // Should fail 'in' validation rule
});
