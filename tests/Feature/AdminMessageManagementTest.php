<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Message;
use App\Models\UserAdditionalInfo;
use Livewire\Livewire;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Messages\ManageMessages;
use function Pest\Laravel\actingAs;
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

test('admin can view detailed message state', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $sender = User::factory()->create(['firstname' => 'TestSender']);
    UserAdditionalInfo::factory()->create(['user_id' => $sender->id, 'username' => 'testsender_username']);
    UserGrant::factory()->create(['user_id' => $sender->id]);

    $receiver = User::factory()->create(['firstname' => 'TestReceiver']);
    UserAdditionalInfo::factory()->create(['user_id' => $receiver->id, 'username' => 'testreceiver_username']);
    UserGrant::factory()->create(['user_id' => $receiver->id]);

    $now = Carbon::now(); // Use a consistent $now for all timestamps in this test
    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'subject' => 'Detailed Status Test Subject',
        'body' => 'Message body for detailed view.',
        'created_at' => $now->copy()->subDays(5),
        'read_at' => $now->copy()->subDays(4),
        'sender_archived_at' => $now->copy()->subDays(3),
        'receiver_deleted_at' => $now->copy()->subDays(2),
        'sender_permanently_deleted_at' => $now->copy()->subDays(1),
        'receiver_permanently_deleted_at' => null, // Example: receiver hasn't perm deleted
        'deleted_at' => $now->copy()->subHour(), // Admin soft deleted
    ]);

    $senderArchivedFormatted = $now->copy()->subDays(3)->format('Y-m-d H:i:s T');
    $senderPermDeletedFormatted = $now->copy()->subDays(1)->format('Y-m-d H:i:s T');
    $receiverDeletedFormatted = $now->copy()->subDays(2)->format('Y-m-d H:i:s T');
    $adminSoftDeletedFormatted = $now->copy()->subHour()->format('Y-m-d H:i:s T');

    get(route('admin.messages.show', ['messageId' => $message->id]))
        ->assertOk()
        ->assertSee('Message Details', false) // Escape false for all HTML content checks
        ->assertSee('Detailed Status Test Subject', false)
        ->assertSee('testsender_username', false)
        ->assertSee('testreceiver_username', false)
        ->assertSee('Message body for detailed view.', false) // Body might have HTML, be careful or assert parts

        // Sender Status Checks with $escape = false
        ->assertSeeInOrder([
            __('Sender Status'), // This is plain text, default escape is fine
            __('Archived:'),
            '<span class="font-semibold">' . $senderArchivedFormatted . '</span>'
        ], false) // false for the order that includes HTML
        ->assertSeeInOrder([
            __('In Trash (Deleted):'),
            '<span class="font-semibold">' . __('N/A') . '</span>' // Sender didn't delete this one
        ], false)
        ->assertSeeInOrder([
            __('Permanently Deleted from Trash:'),
            '<span class="font-semibold">' . $senderPermDeletedFormatted . '</span>'
        ], false)

        // Receiver Status Checks with $escape = false
        ->assertSeeInOrder([
            __('Receiver Status'),
            __('Archived:'),
            '<span class="font-semibold">' . __('N/A') . '</span>' // Receiver didn't archive
        ], false)
        ->assertSeeInOrder([
            __('In Trash (Deleted):'),
            '<span class="font-semibold">' . $receiverDeletedFormatted . '</span>'
        ], false)
        ->assertSeeInOrder([
            __('Permanently Deleted from Trash:'),
            '<span class="font-semibold">' . __('N/A') . '</span>' // Receiver didn't perm delete
        ], false)

        // Admin System Status Checks with $escape = false
        ->assertSeeInOrder([
            __('Admin System Status'),
            __('Soft Deleted by Admin:'),
            '<span class="font-semibold">' . $adminSoftDeletedFormatted . '</span>'
        ], false);
});