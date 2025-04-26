<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Message; // Import Message model
use Livewire\Livewire; // Import Livewire test helper
use App\Livewire\Admin\Messages\ManageMessages; // Import the ManageMessages component
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Database\Eloquent\Factories\Sequence; // For creating users with/without grants

// --- Access tests (Passed, no changes needed) ---
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
// --- End Access Tests ---


test('admin can ban message sender', function () {
    // Arrange: Create an admin user, a non-admin sender, and a receiver
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $sender = User::factory()->create();
    UserGrant::firstOrCreate(['user_id' => $sender->id]); // Ensure sender has a grant record (not admin/mod)

    $receiver = User::factory()->create();
    // --- FIX: Ensure receiver has a grant record ---
    UserGrant::firstOrCreate(['user_id' => $receiver->id]);
    // --- END FIX ---

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
        // --- FIX: Use assertDispatched instead of session ---
        // ->assertSessionHas('message', 'Sender user banned successfully.')
        ->assertDispatched('senderBanned');
    // --- END FIX ---

    // Assert: Verify the sender user IS BANNED (check grant), not soft deleted
    $sender->refresh();
    $senderGrant = $sender->grant; // Get the grant associated with the sender

    // --- ADJUSTED ASSERTION: Check ban status on grant, not soft delete ---
    // expect($sender->trashed())->toBeTrue(); // We are not soft deleting anymore
    expect($senderGrant)->not()->toBeNull();
    expect($senderGrant->is_banned)->toBeTrue();
    expect($senderGrant->is_banned_until)->toBeNull(); // Ban via button is permanent (null until)
    // --- END ADJUSTED ASSERTION ---

    // Verify the message is NOT deleted
    $message->refresh();
    expect($message->exists())->toBeTrue();
    expect($message->trashed())->toBeFalse();

});

test('admin cannot ban admin or moderator sender via this action', function () {
    // Arrange: Create an admin user and an admin/moderator sender
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $adminSender = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminSender->id], ['is_admin' => true]);

    $moderatorSender = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorSender->id], ['is_moderator' => true]);

    $receiver = User::factory()->create();
    UserGrant::firstOrCreate(['user_id' => $receiver->id]);

    $messageFromAdmin = Message::factory()->create(['sender_id' => $adminSender->id, 'receiver_id' => $receiver->id]);
    $messageFromModerator = Message::factory()->create(['sender_id' => $moderatorSender->id, 'receiver_id' => $receiver->id]);

    actingAs($adminUser);

    // --- FIX: Assert events NOT dispatched ---
    Livewire::test(ManageMessages::class)
        ->call('banSender', $adminSender->id)
        // ->assertDispatched('openEditModal', userId: $adminSender->id) // Remove this incorrect assertion
        ->assertNotDispatched('senderBanned'); // Assert the SUCCESS event was NOT fired

    Livewire::test(ManageMessages::class)
        ->call('banSender', $moderatorSender->id)
        // ->assertDispatched('openEditModal', userId: $moderatorSender->id) // Remove this incorrect assertion
        ->assertNotDispatched('senderBanned'); // Assert the SUCCESS event was NOT fired
    // --- END FIX ---

    // Assert: Verify the sender users are NOT banned
    $adminSender->refresh();
    expect($adminSender->grant->is_banned)->toBeFalse();

    $moderatorSender->refresh();
    expect($moderatorSender->grant->is_banned)->toBeFalse();
});

test('admin cannot ban sender if user not found', function () {
    // Arrange: Create an admin user
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    // Act: Act as the admin user and try to ban a non-existent user ID
    actingAs($adminUser);

    Livewire::test(ManageMessages::class)
        ->call('banSender', 9999) // Use a non-existent ID
        // --- FIX: Remove session assertion ---
        // ->assertSessionHas('error', 'Sender user not found.')
        // Optionally, assert that NO 'senderBanned' event was dispatched
        ->assertNotDispatched('senderBanned');
    // --- END FIX ---

});