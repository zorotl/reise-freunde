<?php

use App\Models\User;
use App\Models\UserGrant;
use Livewire\Livewire;
use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Admin\Users\EditUserModal;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Database\Eloquent\Factories\Sequence;

test('admin can update user details and roles', function () {
    // Arrange: Create an admin user and a target user with a grant
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $targetUser = User::factory()->create();
    // Ensure grant exists for the target user
    $targetGrant = UserGrant::firstOrCreate(['user_id' => $targetUser->id], [
        'is_admin' => false,
        'is_moderator' => false,
        'is_banned' => false,
        'is_banned_until' => null,
    ]);


    // Act: Act as the admin user and interact with the modal component
    actingAs($adminUser);

    Livewire::test(EditUserModal::class)
        // Call the method to open the modal and load the target user data
        ->call('openEditModal', $targetUser->id)
        // Assert that properties are loaded
        ->assertSet('userId', $targetUser->id)
        ->assertSet('name', $targetUser->name)
        ->assertSet('email', $targetUser->email)
        ->assertSet('is_admin', false)
        ->assertSet('is_moderator', false)
        ->assertSet('is_banned', false)
        ->assertSet('banned_until', null) // Check the correct property name here too if needed
        ->assertSet('show', true) // Assert modal is open

        // Modify the properties like a user would interact with the form
        ->set('name', 'Updated Name')
        ->set('email', 'updated.email@example.com')
        ->set('is_admin', true) // Grant admin role
        ->set('is_banned', true) // Ban the user
        // --- FIX: Use correct property name 'banned_until' ---
        ->set('banned_until', now()->addDays(7)->format('Y-m-d')) // Set ban until date
        // --- END FIX ---

        // Call the save method
        ->call('saveUser')
        // Assert validation passes and modal closes
        ->assertHasNoErrors()
        ->assertSet('show', false)
        // Assert an event is dispatched to refresh the list
        ->assertDispatched('userUpdated');

    // Assert: Verify the database was updated correctly
    $targetUser->refresh(); // Refresh the user model from DB
    $targetGrant->refresh(); // Refresh the grant model from DB

    expect($targetUser->name)->toBe('Updated Name');
    expect($targetUser->email)->toBe('updated.email@example.com');
    expect($targetGrant->is_admin)->toBeTrue();
    expect($targetGrant->is_moderator)->toBeFalse(); // Ensure moderator wasn't set unintentionally
    expect($targetGrant->is_banned)->toBeTrue();
    // Check if banned_until is set and is roughly within the expected date range (allow for slight time differences)
    expect($targetGrant->is_banned_until)->not()->toBeNull();
    expect($targetGrant->is_banned_until->startOfDay()->equalTo(now()->addDays(7)->startOfDay()))->toBeTrue();

});

// This test passed before, no change needed here
test('admin can remove user roles and ban status', function () {
    // Arrange: Create an admin user and a target user who is admin, moderator, and banned
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $targetUser = User::factory()->create();
    $targetGrant = UserGrant::updateOrCreate(['user_id' => $targetUser->id], [
        'is_admin' => true,
        'is_moderator' => true,
        'is_banned' => true,
        'is_banned_until' => now()->addDays(7),
    ]);

    // Act: Act as the admin user and interact with the modal component
    actingAs($adminUser);

    Livewire::test(EditUserModal::class)
        ->call('openEditModal', $targetUser->id)
        // Assert properties are loaded correctly
        ->assertSet('is_admin', true)
        ->assertSet('is_moderator', true)
        ->assertSet('is_banned', true)

        // Modify properties to remove roles and ban
        ->set('is_admin', false)
        ->set('is_moderator', false)
        ->set('is_banned', false) // Unban the user

        // Call the save method
        ->call('saveUser')
        ->assertHasNoErrors()
        ->assertSet('show', false)
        ->assertDispatched('userUpdated');

    // Assert: Verify the database was updated correctly
    $targetGrant->refresh();

    expect($targetGrant->is_admin)->toBeFalse();
    expect($targetGrant->is_moderator)->toBeFalse();
    expect($targetGrant->is_banned)->toBeFalse();
    expect($targetGrant->is_banned_until)->toBeNull(); // Ensure banned_until is null when not banned

});


test('edit user validation works', function () {
    // Arrange: Create an admin user and a target user
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $targetUser = User::factory()->create();
    UserGrant::firstOrCreate(['user_id' => $targetUser->id]); // Ensure grant exists

    // Act: Act as the admin user and interact with the modal component
    actingAs($adminUser);

    Livewire::test(EditUserModal::class)
        ->call('openEditModal', $targetUser->id)
        // Attempt to save with invalid data
        ->set('name', '') // Invalid: required
        ->set('email', 'invalid-email') // Invalid: not an email
        ->set('is_banned', true)
        // --- FIX: Use correct property name 'banned_until' ---
        ->set('banned_until', 'invalid-date') // Invalid date format
        // --- END FIX ---

        ->call('saveUser')
        // Assert validation errors
        ->assertHasErrors(['name', 'email', 'banned_until']) // Check correct property name in error
        // Assert modal stays open on validation errors
        ->assertSet('show', true);

    // --- Second part of the validation test ---
    // Reset the component state or start a new test instance for clarity
    Livewire::test(EditUserModal::class)
        ->call('openEditModal', $targetUser->id) // Re-open modal if needed, or assume state persists if designed so
        // Attempt to save with ban enabled but invalid date
        ->set('is_banned', true)
        // --- FIX: Use correct property name 'banned_until' ---
        ->set('banned_until', now()->subDay()->format('Y-m-d')) // Invalid: Must be after_or_equal:today
        // --- END FIX ---

        ->call('saveUser')
        // Assert validation errors for banned_until
        ->assertHasErrors(['banned_until'])
        ->assertSet('show', true); // Modal should stay open
});