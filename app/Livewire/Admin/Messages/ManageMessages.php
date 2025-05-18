<?php

namespace App\Livewire\Admin\Messages;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use App\Models\UserGrant;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminForceDeletedMessageNotification;

class ManageMessages extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    #[On('messageUpdated')]
    #[On('senderBanned')]
    #[On('messageDeleted')]
    #[On('messageRestored')]
    #[On('adminMessageActionFeedback')] // New listener for our custom event
    public function refreshMessageListWithMessage($message = null, $type = 'message'): void
    {
        if ($message) {
            session()->flash($type, $message); // Re-flash for display if needed
        }
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    #[Computed()]
    public function isAdminOrModerator()
    {
        $user = Auth::user();
        return $user && $user->isAdminOrModerator();
    }

    public function banSender($senderId)
    {
        if (!$this->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: 'You are not authorized to perform this action.', type: 'error');
            return;
        }
        $sender = User::with('grant')->find($senderId);
        if (!$sender) {
            $this->dispatch('adminMessageActionFeedback', message: 'Sender user not found.', type: 'error');
            return;
        }
        if ($sender->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: 'Cannot ban an admin or moderator via this action.', type: 'error');
            return;
        }
        $this->dispatch('openEditModal', userId: $senderId);
        $this->dispatch('adminMessageActionFeedback', message: 'Manage ban for ' . $sender->name . ' in the user edit modal.', type: 'message');
    }

    public function adminSoftDeleteMessage(int $messageId): void
    {
        if (!$this->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: 'Unauthorized action.', type: 'error');
            return;
        }
        $message = Message::find($messageId);
        if ($message) {
            $message->delete();
            $this->dispatch('adminMessageActionFeedback', message: 'Message soft-deleted by admin.', type: 'message');
            $this->dispatch('messageDeleted'); // For UI refresh
        } else {
            $this->dispatch('adminMessageActionFeedback', message: 'Message not found for soft deletion.', type: 'error');
        }
    }

    public function restoreMessage(int $messageId): void
    {
        if (!$this->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: 'Unauthorized action.', type: 'error');
            return;
        }
        $message = Message::withTrashed()->find($messageId);
        if ($message && $message->trashed()) {
            $message->restore();
            $this->dispatch('adminMessageActionFeedback', message: 'Message restored by admin.', type: 'message');
            $this->dispatch('messageRestored');
        } else {
            $this->dispatch('adminMessageActionFeedback', message: 'Message not found or not deleted.', type: 'error');
        }
    }

    public function forceDeleteMessage(int $messageId): void
    {
        if (!$this->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: 'Unauthorized action.', type: 'error');
            return;
        }
        $message = Message::withTrashed()->with(['sender', 'receiver'])->find($messageId);

        if ($message) {
            $originalSubject = $message->subject;
            $originalMessageId = $message->id;
            $originalSenderId = $message->sender_id; // Get sender ID
            $originalReceiverId = $message->receiver_id; // Get receiver ID
            $senderUser = $message->sender; // Get sender model
            $receiverUser = $message->receiver; // Get receiver model
            $adminPerformingAction = Auth::user();

            try {
                $message->forceDelete();
                $this->dispatch('adminMessageActionFeedback', message: 'Message permanently deleted by admin.', type: 'message');
                $this->dispatch('messageDeleted');

                if ($adminPerformingAction) {
                    $adminName = $adminPerformingAction->firstname . ' ' . $adminPerformingAction->lastname; // Use firstname and lastname

                    // Notify sender if they exist and are not the admin performing the action
                    if ($senderUser && $senderUser->id !== $adminPerformingAction->id) {
                        $senderUser->notify(new AdminForceDeletedMessageNotification($originalSubject, $originalMessageId, $adminName, $originalSenderId, $originalReceiverId));
                    }
                    // Notify receiver if they exist and are not the admin performing the action
                    if ($receiverUser && $receiverUser->id !== $adminPerformingAction->id) {
                        $receiverUser->notify(new AdminForceDeletedMessageNotification($originalSubject, $originalMessageId, $adminName, $originalSenderId, $originalReceiverId));
                    }
                }
            } catch (\Exception $e) {
                Log::error("Admin force delete message error: {$e->getMessage()}", ['message_id' => $originalMessageId]);
                $this->dispatch('adminMessageActionFeedback', message: 'Failed to permanently delete message.', type: 'error');
            }
        } else {
            $this->dispatch('adminMessageActionFeedback', message: 'Message not found for permanent deletion.', type: 'error');
        }
    }

    public function render()
    {
        // ... render logic remains the same ...
        $query = Message::withTrashed() // Admins see system soft-deleted messages
            ->with([
                // Eager load with specific columns, including trashed users
                'sender' => fn($q) => $q->withTrashed()->select('id', 'firstname', 'lastname', 'email')->with(['grant:user_id,is_banned', 'additionalInfo:user_id,username']),
                'receiver' => fn($q) => $q->withTrashed()->select('id', 'firstname', 'lastname', 'email')->with(['grant:user_id,is_banned', 'additionalInfo:user_id,username']),
            ])
            // For the main message, select only necessary columns for the list view if possible.
            // If admin needs all fields for this overview, then '*' is fine, but be mindful.
            // Example: ->select('messages.id', 'messages.subject', 'messages.sender_id', 'messages.receiver_id', 'messages.created_at', 'messages.read_at', 'messages.deleted_at', 'messages.sender_deleted_at', ...)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('body', 'like', '%' . $this->search . '%')
                        ->orWhereHas(
                            'sender',
                            fn($sq) =>
                            $sq->where('firstname', 'like', '%' . $this->search . '%')
                                ->orWhere('lastname', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%')
                                ->orWhereHas('additionalInfo', fn($aiq) => $aiq->where('username', 'like', '%' . $this->search . '%'))
                        )
                        ->orWhereHas(
                            'receiver',
                            fn($rq) =>
                            $rq->where('firstname', 'like', '%' . $this->search . '%')
                                ->orWhere('lastname', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%')
                                ->orWhereHas('additionalInfo', fn($aiq) => $aiq->where('username', 'like', '%' . $this->search . '%'))
                        );
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $messages = $query->paginate($this->perPage);

        return view('livewire.admin.messages.manage-messages', [
            'messages' => $messages,
        ]);
    }
}