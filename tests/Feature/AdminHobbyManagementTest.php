<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Hobby; // Import Hobby model
use Livewire\Livewire;
use App\Livewire\Admin\Hobbies\ManageHobbies;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// ... admin access tests

test('non admin/moderator users cannot access admin hobby management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete();

    actingAs($user)
        ->get(route('admin.hobbies'))
        ->assertForbidden(); // Or your redirect route
});

test('admin users can access admin hobby management', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.hobbies'))
        ->assertOk();
});

test('moderator users can access admin hobby management', function () {
    $moderatorUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorUser->id], ['is_moderator' => true]);

    actingAs($moderatorUser)
        ->get(route('admin.hobbies'))
        ->assertOk();
});

test('guest users are redirected to login when trying to access admin hobby management', function () {
    get(route('admin.hobbies'))
        ->assertRedirect(route('login'));
});


test('admin can add a new hobby', function () {
    // Arrange: Create an admin user
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    // Act: Act as admin and interact with the component
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->set('name', 'New Hobby Name') // Set the name property
        ->call('saveHobby') // Call the save method
        ->assertHasNoErrors()
        // Assert a success message is flashed
        ->assertSessionHas('message', 'Hobby added successfully.')
        // Assert the form is reset
        ->assertSet('name', '')
        ->assertSet('editingHobbyId', null)
        // Assert an event is dispatched
        ->assertDispatched('hobbyAdded');

    // Assert: Verify the hobby was created in the database
    expect(Hobby::where('name', 'New Hobby Name')->exists())->toBeTrue();
});

test('admin can edit an existing hobby', function () {
    // Arrange: Create an admin user and a hobby
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create(['name' => 'Original Hobby']);

    // Act: Act as admin and interact with the component
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        // Load the hobby into the form
        ->call('editHobby', $hobby->id)
        // Assert properties are set
        ->assertSet('editingHobbyId', $hobby->id)
        ->assertSet('name', 'Original Hobby')

        // Modify the name
        ->set('name', 'Updated Hobby Name')

        // Save the changes
        ->call('saveHobby')
        ->assertHasNoErrors()
        // Assert a success message is flashed
        ->assertSessionHas('message', 'Hobby updated successfully.')
        // Assert the form is reset
        ->assertSet('name', '')
        ->assertSet('editingHobbyId', null)
        // Assert an event is dispatched
        ->assertDispatched('hobbyUpdated');

    // Assert: Verify the hobby was updated in the database
    $hobby->refresh();
    expect($hobby->name)->toBe('Updated Hobby Name');
});

test('admin can soft delete a hobby', function () {
    // Arrange: Create an admin user and a hobby
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();

    // Assert: Hobby is not soft deleted initially
    expect($hobby->trashed())->toBeFalse();

    // Act: Act as admin and call soft delete
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('softDeleteHobby', $hobby->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Hobby soft deleted successfully.')
        ->assertDispatched('hobbyDeleted');

    // Assert: Hobby is now soft deleted
    $hobby->refresh();
    expect($hobby->trashed())->toBeTrue();
});

test('admin can restore a soft deleted hobby', function () {
    // Arrange: Create an admin user and a soft deleted hobby
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();
    $hobby->delete(); // Soft delete the hobby

    // Assert: Hobby is soft deleted initially
    expect($hobby->trashed())->toBeTrue();

    // Act: Act as admin and call restore
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('restoreHobby', $hobby->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Hobby restored successfully.')
        ->assertDispatched('hobbyRestored');

    // Assert: Hobby is no longer soft deleted
    $hobby->refresh();
    expect($hobby->trashed())->toBeFalse();
});

test('admin can force delete a hobby', function () {
    // Arrange: Create an admin user and a soft deleted hobby
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();
    $hobby->delete(); // Soft delete the hobby

    // Assert: Hobby exists (even if soft deleted)
    expect(Hobby::withTrashed()->find($hobby->id))->not()->toBeNull();

    // Act: Act as admin and call force delete
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('forceDeleteHobby', $hobby->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Hobby permanently deleted.')
        ->assertDispatched('hobbyDeleted');

    // Assert: Hobby no longer exists in the database (even with withTrashed)
    expect(Hobby::withTrashed()->find($hobby->id))->toBeNull();
});

test('hobby validation works', function () {
    // Arrange: Create an admin user and a hobby
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $existingHobby = Hobby::factory()->create(['name' => 'Existing Hobby']);

    // Act: Act as admin and try to add invalid data
    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        // Attempt to add with empty name
        ->set('name', '')
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'required']);

    Livewire::test(ManageHobbies::class)
        // Attempt to add with non-unique name
        ->set('name', 'Existing Hobby')
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageHobbies::class)
        // Attempt to edit with non-unique name (different hobby)
        ->call('editHobby', $existingHobby->id) // Load existing hobby
        ->set('name', Hobby::factory()->create()->name) // Set name to another existing hobby's name
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageHobbies::class)
        // Attempt to edit with its own name (should pass unique rule)
        ->call('editHobby', $existingHobby->id) // Load existing hobby
        ->set('name', 'Existing Hobby') // Set name back to its original name
        ->call('saveHobby')
        ->assertHasNoErrors(); // Should pass
});