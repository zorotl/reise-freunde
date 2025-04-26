<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Hobby; // Import Hobby model
use Livewire\Livewire;
use App\Livewire\Admin\Hobbies\ManageHobbies;
// Remove Session facade if no longer needed: use Illuminate\Support\Facades\Session;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// --- Access tests (Passed, no changes needed) ---
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
// --- End Access Tests ---


test('admin can add a new hobby', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->set('name', 'New Hobby Name')
        ->call('saveHobby')
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Hobby added successfully.')
        ->assertDispatched('hobbyAdded')
        // --- END FIX ---
        ->assertSet('name', '')
        ->assertSet('editingHobbyId', null);

    expect(Hobby::where('name', 'New Hobby Name')->exists())->toBeTrue();
});

test('admin can edit an existing hobby', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create(['name' => 'Original Hobby']);

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('editHobby', $hobby->id)
        ->assertSet('editingHobbyId', $hobby->id)
        ->assertSet('name', 'Original Hobby')
        ->set('name', 'Updated Hobby Name')
        ->call('saveHobby')
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Hobby updated successfully.')
        ->assertDispatched('hobbyUpdated')
        // --- END FIX ---
        ->assertSet('name', '')
        ->assertSet('editingHobbyId', null);

    $hobby->refresh();
    expect($hobby->name)->toBe('Updated Hobby Name');
});

test('admin can soft delete a hobby', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();

    expect($hobby->trashed())->toBeFalse();

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('softDeleteHobby', $hobby->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Hobby soft deleted successfully.')
        ->assertDispatched('hobbyDeleted');
    // --- END FIX ---

    $hobby->refresh();
    expect($hobby->trashed())->toBeTrue();
});

test('admin can restore a soft deleted hobby', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();
    $hobby->delete();

    expect($hobby->trashed())->toBeTrue();

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('restoreHobby', $hobby->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Hobby restored successfully.')
        ->assertDispatched('hobbyRestored');
    // --- END FIX ---

    $hobby->refresh();
    expect($hobby->trashed())->toBeFalse();
});

test('admin can force delete a hobby', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $hobby = Hobby::factory()->create();
    $hobby->delete();

    expect(Hobby::withTrashed()->find($hobby->id))->not()->toBeNull();

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->call('forceDeleteHobby', $hobby->id)
        ->assertHasNoErrors()
        // --- FIX: Assert event dispatched instead of session ---
        // ->assertSessionHas('message', 'Hobby permanently deleted.')
        ->assertDispatched('hobbyDeleted'); // Assuming same event for force delete
    // --- END FIX ---

    expect(Hobby::withTrashed()->find($hobby->id))->toBeNull();
});

// This test passed before, no change needed
test('hobby validation works', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $existingHobby = Hobby::factory()->create(['name' => 'Existing Hobby']);

    actingAs($adminUser);

    Livewire::test(ManageHobbies::class)
        ->set('name', '')
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'required']);

    Livewire::test(ManageHobbies::class)
        ->set('name', 'Existing Hobby')
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageHobbies::class)
        ->call('editHobby', $existingHobby->id)
        ->set('name', Hobby::factory()->create()->name)
        ->call('saveHobby')
        ->assertHasErrors(['name' => 'unique']);

    Livewire::test(ManageHobbies::class)
        ->call('editHobby', $existingHobby->id)
        ->set('name', 'Existing Hobby')
        ->call('saveHobby')
        ->assertHasNoErrors();
});