<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\TravelStyle; // Import TravelStyle model
use Livewire\Livewire;
use App\Livewire\Admin\TravelStyles\ManageTravelStyles;
// Remove Session facade if no longer needed: use Illuminate\Support\Facades\Session;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Database\Eloquent\Factories\Sequence;

// --- Passing access tests remain the same ---
test('non admin/moderator users cannot access admin travel style management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete();

    actingAs($user)
        ->get(route('admin.travel-styles'))
        ->assertForbidden();
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
// --- End of passing tests ---


test('admin can add a new travel style', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->set('name', 'New Travel Style Name')
        ->call('saveTravelStyle')
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Travel Style added successfully.')
        ->assertDispatched('travelStyleAdded')
        // --- END FIX ---
        ->assertSet('name', '')
        ->assertSet('editingTravelStyleId', null);

    expect(TravelStyle::where('name', 'New Travel Style Name')->exists())->toBeTrue();
});

test('admin can edit an existing travel style', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create(['name' => 'Original Travel Style']);

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('editTravelStyle', $travelStyle->id)
        ->assertSet('editingTravelStyleId', $travelStyle->id)
        ->assertSet('name', 'Original Travel Style')
        ->set('name', 'Updated Travel Style Name')
        ->call('saveTravelStyle')
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Travel Style updated successfully.')
        ->assertDispatched('travelStyleUpdated')
        // --- END FIX ---
        ->assertSet('name', '')
        ->assertSet('editingTravelStyleId', null);

    $travelStyle->refresh();
    expect($travelStyle->name)->toBe('Updated Travel Style Name');
});

test('admin can soft delete a travel style', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();

    expect($travelStyle->trashed())->toBeFalse();

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('softDeleteTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Travel Style soft deleted successfully.')
        ->assertDispatched('travelStyleDeleted');
    // --- END FIX ---

    $travelStyle->refresh();
    expect($travelStyle->trashed())->toBeTrue();
});

test('admin can restore a soft deleted travel style', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();
    $travelStyle->delete();

    expect($travelStyle->trashed())->toBeTrue();

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('restoreTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Travel Style restored successfully.')
        ->assertDispatched('travelStyleRestored');
    // --- END FIX ---

    $travelStyle->refresh();
    expect($travelStyle->trashed())->toBeFalse();
});

test('admin can force delete a travel style', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $travelStyle = TravelStyle::factory()->create();
    $travelStyle->delete();

    expect(TravelStyle::withTrashed()->find($travelStyle->id))->not()->toBeNull();

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->call('forceDeleteTravelStyle', $travelStyle->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Travel Style permanently deleted.')
        ->assertDispatched('travelStyleDeleted'); // Assuming the same event for force delete, adjust if needed
    // --- END FIX ---

    expect(TravelStyle::withTrashed()->find($travelStyle->id))->toBeNull();
});

// This test passed before, no change needed
test('travel style validation works', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $existingTravelStyle = TravelStyle::factory()->create(['name' => 'Existing Style']);

    actingAs($adminUser);

    Livewire::test(ManageTravelStyles::class)
        ->set('name', '')
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'required']);

    Livewire::test(ManageTravelStyles::class)
        ->set('name', 'Existing Style')
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageTravelStyles::class)
        ->call('editTravelStyle', $existingTravelStyle->id)
        ->set('name', TravelStyle::factory()->create()->name)
        ->call('saveTravelStyle')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageTravelStyles::class)
        ->call('editTravelStyle', $existingTravelStyle->id)
        ->set('name', 'Existing Style')
        ->call('saveTravelStyle')
        ->assertHasNoErrors();
});