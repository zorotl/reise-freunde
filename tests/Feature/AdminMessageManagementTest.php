<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Message; // Import Message model
use Livewire\Livewire; // Import Livewire test helper
use App\Livewire\Admin\Messages\ManageMessages; // Import the ManageMessages component

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Database\Eloquent\Factories\Sequence; // For creating users with/without grants

// ... existing admin access tests if adding to a shared file

test('non admin/moderator users cannot access admin message management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete();

    actingAs($user)
        ->get(route('admin.messages'))
        ->assertForbidden(); // Or your redirect route
});

test('admin users can access admin message management', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.messages'))
        ->assertOk();
});

test('moderator users can access admin message management', function () {
    $moderatorUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorUser->id], ['is_moderator' => true]);

    actingAs($moderatorUser)
        ->get(route('admin.messages'))
        ->assertOk();
});

test('guest users are redirected to login when trying to access admin message management', function () {
    get(route('admin.messages'))
        ->assertRedirect(route('login'));
});


test('admin can ban message sender (soft delete user)', function () {
    // Arrange: Create an admin user, a non-admin sender, and a receiver
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $sender = User::factory()->create();
    UserGrant::firstOrCreate(['user_id' => $sender->id]); // Ensure sender has a grant record (not admin/mod)

    $receiver = User::factory()->create();

    // Create a message from the sender
    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'subject' => 'Test Subject',
        'body' => 'Test Body',
    ]);

    // Act: Act as the admin user and call the banSender method
    actingAs($adminUser);

    Livewire::test(ManageMessages::class)
        ->call('banSender', $sender->id)
        ->assertHasNoErrors()
        // Assert a success message is flashed
        ->assertSessionHas('message', 'Sender user banned successfully.')
        // Assert an event is dispatched to refresh the list
        ->assertDispatched('senderBanned');

    // Assert: Verify the sender user is soft deleted
    $sender->refresh();
    expect($sender->trashed())->toBeTrue();

    // Verify the message is NOT deleted (only the sender user)
    $message->refresh();
    expect($message->exists())->toBeTrue();
    expect($message->trashed())->toBeFalse(); // Message itself is not soft deleted

    // Verify the sender is still retrievable with withTrashed()
    $softDeletedSender = User::withTrashed()->find($sender->id);
    expect($softDeletedSender)->not()->toBeNull();
    expect($softDeletedSender->id)->toBe($sender->id);
});

test('admin cannot ban admin or moderator sender via this action', function () {
    // Arrange: Create an admin user and an admin/moderator sender
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $adminSender = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminSender->id], ['is_admin' => true]); // Make sender an admin

    $moderatorSender = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorSender->id], ['is_moderator' => true]); // Make sender a moderator

    $receiver = User::factory()->create();

    // Create messages from admin/moderator senders
    $messageFromAdmin = Message::factory()->create(['sender_id' => $adminSender->id, 'receiver_id' => $receiver->id]);
    $messageFromModerator = Message::factory()->create(['sender_id' => $moderatorSender->id, 'receiver_id' => $receiver->id]);


    // Act: Act as the admin user and try to ban admin/moderator senders
    actingAs($adminUser);

    Livewire::test(ManageMessages::class)
        ->call('banSender', $adminSender->id)
        // Assert an error message is flashed
        ->assertSessionHas('error', 'Cannot ban an admin or moderator via this action.');

    Livewire::test(ManageMessages::class)
        ->call('banSender', $moderatorSender->id)
        ->assertSessionHas('error', 'Cannot ban an admin or moderator via this action.');


    // Assert: Verify the sender users are NOT soft deleted
    $adminSender->refresh();
    expect($adminSender->trashed())->toBeFalse();

    $moderatorSender->refresh();
    expect($moderatorSender->trashed())->toBeFalse();
});

test('admin cannot ban sender if user not found', function () {
    // Arrange: Create an admin user
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    // Act: Act as the admin user and try to ban a non-existent user ID
    actingAs($adminUser);

    Livewire::test(ManageMessages::class)
        ->call('banSender', 9999) // Use a non-existent ID
        // Assert an error message is flashed
        ->assertSessionHas('error', 'Sender user not found.');

});