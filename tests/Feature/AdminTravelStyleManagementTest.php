<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\TravelStyle; // Import TravelStyle model
use Livewire\Livewire;
use App\Livewire\Admin\TravelStyles\ManageTravelStyles;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Database\Eloquent\Factories\Sequence;

// ... admin access tests

test('non admin/moderator users cannot access admin travel style management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete();

    actingAs($user)
        ->get(route('admin.travel-styles'))
        ->assertForbidden(); // Or your redirect route
});

test('admin users can access admin travel style management', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.travel-styles'))
        ->assertOk();
});

test('moderator users can access admin travel style management', function () {
    $moderatorUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorUser->id], ['is_moderator' => true]);

    actingAs($moderatorUser)
        ->get(route('admin.travel-styles'))
        ->assertOk();
});

test('guest users are redirected to login when trying to access admin travel style management', function () {
    get(route('admin.travel-styles'))
        ->assertRedirect(route('login'));
});

test('admin can add a new travel style', function () {
    // Arrange: Create an admin user
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    // Act: Act as admin and interact with the component
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->set('name', 'New Travel Style Name') // Set the name property
        ->call('saveTravelStyle') // Call the save method
        ->assertHasNoErrors()
        // Assert a success message is flashed
        ->assertSessionHas('message', 'Travel Style added successfully.')
        // Assert the form is reset
        ->assertSet('name', '')
        ->assertSet('editingTravelStyleId', null)
        // Assert an event is dispatched
        ->assertDispatched('travelStyleAdded');

    // Assert: Verify the travel style was created in the database
    expect(TravelStyle::where('name', 'New Travel Style Name')->exists())->toBeTrue();
});

test('admin can edit an existing travel style', function () {
    // Arrange: Create an admin user and a travel style
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create(['name' => 'Original Travel Style']);

    // Act: Act as admin and interact with the component
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        // Load the travel style into the form
        ->call('editTravelStyle', $travelStyle->id)
        // Assert properties are set
        ->assertSet('editingTravelStyleId', $travelStyle->id)
        ->assertSet('name', 'Original Travel Style')

        // Modify the name
        ->set('name', 'Updated Travel Style Name')

        // Save the changes
        ->call('saveTravelStyle')
        ->assertHasNoErrors()
        // Assert a success message is flashed
        ->assertSessionHas('message', 'Travel Style updated successfully.')
        // Assert the form is reset
        ->assertSet('name', '')
        ->assertSet('editingTravelStyleId', null)
        // Assert an event is dispatched
        ->assertDispatched('travelStyleUpdated');

    // Assert: Verify the travel style was updated in the database
    $travelStyle->refresh();
    expect($travelStyle->name)->toBe('Updated Travel Style Name');
});

test('admin can soft delete a travel style', function () {
    // Arrange: Create an admin user and a travel style
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();

    // Assert: Travel style is not soft deleted initially
    expect($travelStyle->trashed())->toBeFalse();

    // Act: Act as admin and call soft delete
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('softDeleteTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Travel Style soft deleted successfully.')
        ->assertDispatched('travelStyleDeleted');

    // Assert: Travel style is now soft deleted
    $travelStyle->refresh();
    expect($travelStyle->trashed())->toBeTrue();
});

test('admin can restore a soft deleted travel style', function () {
    // Arrange: Create an admin user and a soft deleted travel style
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();
    $travelStyle->delete(); // Soft delete

    // Assert: Travel style is soft deleted initially
    expect($travelStyle->trashed())->toBeTrue();

    // Act: Act as admin and call restore
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('restoreTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Travel Style restored successfully.')
        ->assertDispatched('travelStyleRestored');

    // Assert: Travel style is no longer soft deleted
    $travelStyle->refresh();
    expect($travelStyle->trashed())->toBeFalse();
});

test('admin can force delete a travel style', function () {
    // Arrange: Create an admin user and a soft deleted travel style
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();
    $travelStyle->delete(); // Soft delete

    // Assert: Travel style exists (even if soft deleted)
    expect(TravelStyle::withTrashed()->find($travelStyle->id))->not()->toBeNull();

    // Act: Act as admin and call force delete
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('forceDeleteTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Travel Style permanently deleted.')
        ->assertDispatched('travelStyleDeleted');

    // Assert: Travel style no longer exists in the database (even with withTrashed)
    expect(TravelStyle::withTrashed()->find($travelStyle->id))->toBeNull();
});

test('travel style validation works', function () {
    // Arrange: Create an admin user and a travel style
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $existingTravelStyle = TravelStyle::factory()->create(['name' => 'Existing Style']);

    // Act: Act as admin and try to add invalid data
    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        // Attempt to add with empty name
        ->set('name', '')
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'required']);

    Livewire::test(ManageTravelStyles::class)
        // Attempt to add with non-unique name
        ->set('name', 'Existing Style')
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageTravelStyles::class)
        // Attempt to edit with non-unique name (different travel style)
        ->call('editTravelStyle', $existingTravelStyle->id) // Load existing
        ->set('name', TravelStyle::factory()->create()->name) // Set name to another existing
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageTravelStyles::class)
        // Attempt to edit with its own name (should pass unique rule)
        ->call('editTravelStyle', $existingTravelStyle->id) // Load existing
        ->set('name', 'Existing Style') // Set name back to original
        ->call('saveTravelStyle')
        ->assertHasNoErrors(); // Should pass
});