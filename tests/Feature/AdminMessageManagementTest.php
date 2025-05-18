<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Message;
use App\Livewire\Admin\Messages\ManageMessages;
use Livewire\Livewire;
use Illuminate\Support\Facades\Auth; // Import Auth
use function Pest\Laravel\actingAs; // Ensure this is used for Pest tests
use function Pest\Laravel\get;

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


test('admin can soft delete a message', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();

    Livewire::test(ManageMessages::class)
        ->call('adminSoftDeleteMessage', $message->id)
        ->assertDispatched('adminMessageActionFeedback', message: 'Message soft-deleted by admin.', type: 'message'); // Assert the event and its payload

    $this->assertSoftDeleted('messages', ['id' => $message->id]);
});

test('admin can restore a soft-deleted message', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();
    $message->delete(); // Soft delete it first

    Livewire::test(ManageMessages::class)
        ->call('restoreMessage', $message->id)
        ->assertDispatched('adminMessageActionFeedback', message: 'Message restored by admin.', type: 'message'); // Assert the event

    $this->assertNotSoftDeleted('messages', ['id' => $message->id]);
});

test('admin can force delete a message', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();

    Livewire::test(ManageMessages::class)
        ->call('forceDeleteMessage', $message->id)
        ->assertDispatched('adminMessageActionFeedback', message: 'Message permanently deleted by admin.', type: 'message'); // Assert the event

    $this->assertDatabaseMissing('messages', ['id' => $message->id]);
});

// ... (admin view shows user-deleted and user-archived messages test remains the same)
test('admin view shows user-deleted and user-archived messages', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Message::factory()->create([
        'sender_id' => $user1->id,
        'receiver_id' => $user2->id,
        'subject' => 'Deleted by Sender',
        'sender_deleted_at' => now(),
    ]);
    Message::factory()->create([
        'sender_id' => $user1->id,
        'receiver_id' => $user2->id,
        'subject' => 'Archived by Receiver',
        'receiver_archived_at' => now(),
    ]);

    Livewire::test(ManageMessages::class)
        ->assertSee('Deleted by Sender')
        ->assertSee('Archived by Receiver');
});