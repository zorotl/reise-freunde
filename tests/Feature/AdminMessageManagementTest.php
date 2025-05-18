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

    $now = Carbon::now();
    $message = Message::factory()->create([
        // ... (message data is the same) ...
        'sender_archived_at' => $now->copy()->subDays(3),
        'receiver_deleted_at' => $now->copy()->subDays(2),
        'sender_permanently_deleted_at' => $now->copy()->subDays(1),
        'receiver_permanently_deleted_at' => null,
        'deleted_at' => $now->copy()->subHour(),
    ]);

    $senderArchivedFormatted = $now->copy()->subDays(3)->format('Y-m-d H:i:s T');
    $senderPermDeletedFormatted = $now->copy()->subDays(1)->format('Y-m-d H:i:s T');
    $receiverDeletedFormatted = $now->copy()->subDays(2)->format('Y-m-d H:i:s T');
    $adminSoftDeletedFormatted = $now->copy()->subHour()->format('Y-m-d H:i:s T');

    // dd($senderArchivedFormatted);

    get(route('admin.messages.show', ['messageId' => $message->id]))
        ->assertOk()
        ->assertSee('Message Details', false)
        ->assertSee('Subject', false)
        ->assertSee('Sender', false)
        ->assertSee('Receiver', false)
        ->assertSee('Message Body', false)
        // Sender Status
        ->assertSee(__('Sender Actions'), false) // The heading for the section
        ->assertSee(__('Archived:') . ' <span class="font-semibold">' . $senderArchivedFormatted . '</span>', false)
        ->assertSee(__('In Trash (Deleted):') . ' <span class="font-semibold">' . __('N/A') . '</span>', false)
        ->assertSee(__('Perm. Deleted from Trash:') . ' <span class="font-semibold">' . $senderPermDeletedFormatted . '</span>', false)

        // Receiver Status
        ->assertSee(__('Receiver Actions'), false)
        ->assertSee(__('Archived:') . ' <span class="font-semibold">' . __('N/A') . '</span>', false)
        ->assertSee(__('In Trash (Deleted):') . ' <span class="font-semibold">' . $receiverDeletedFormatted . '</span>', false)
        ->assertSee(__('Perm. Deleted from Trash:') . ' <span class="font-semibold">' . __('N/A') . '</span>', false)

        // Admin System Status
        ->assertSee(__('Admin System Status'), false)
        ->assertSee(__('Soft Deleted by Admin:') . ' <span class="font-semibold">' . $adminSoftDeletedFormatted . '</span>', false);
});

test('admin can soft delete a message from its detail view page', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();

    // Initial state: Not soft-deleted
    $this->assertNotSoftDeleted('messages', ['id' => $message->id]);

    // Given the Volt structure, let's test the methods by loading the component
    // and passing the messageId.
    Livewire::actingAs($admin)
        ->test('pages.admin.messages.show', ['messageId' => $message->id]) // Mounts the Volt component
        ->assertSee($message->subject) // Ensure message is loaded
        ->call('adminSoftDeleteMessage')
        ->assertRedirect(route('admin.messages'))
        ->assertSessionHas('message', __('Message soft-deleted by admin.'));

    $this->assertSoftDeleted('messages', ['id' => $message->id]);
});

test('admin can restore a message from its detail view page', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();
    $message->delete(); // Soft delete it first
    $this->assertSoftDeleted('messages', ['id' => $message->id]);

    Livewire::actingAs($admin)
        ->test('pages.admin.messages.show', ['messageId' => $message->id])
        ->assertSee($message->subject)
        ->call('restoreMessage')
        ->assertDispatched('adminMessageActionFeedback', message: __('Message restored by admin.'), type: 'message'); // Check event

    $this->assertNotSoftDeleted('messages', ['id' => $message->id]);
    // Check if message is still visible and buttons updated
    // This requires asserting the view state after the call, which Livewire::test facilitates.
    Livewire::actingAs($admin)
        ->test('pages.admin.messages.show', ['messageId' => $message->id])
        ->assertDontSee(__('Restore (Admin)')); // Restore button should be gone
});

test('admin can force delete a message from its detail view page', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $message = Message::factory()->create();
    $messageId = $message->id; // Store ID before it's deleted

    Livewire::actingAs($admin)
        ->test('pages.admin.messages.show', ['messageId' => $messageId])
        ->assertSee($message->subject)
        ->call('forceDeleteMessage')
        ->assertRedirect(route('admin.messages'))
        ->assertSessionHas('message', __('Message permanently deleted by admin.'));

    $this->assertDatabaseMissing('messages', ['id' => $messageId]);
});