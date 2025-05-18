<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Trash - Mail')]
class TrashBox extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[On('messageRestored')]
    #[On('messagePermanentlyDeletedFromTrash')]
    #[On('userMessageActionFeedback')]
    public function handleFeedback($message = null, $type = 'status'): void
    {
        if ($message) {
            session()->flash($type, $message);
        }
        $this->resetPage();
    }

    public function getMessagesProperty()
    {
        if (!Auth::check()) {
            return Message::query()->whereRaw('1 = 0')->paginate(10);
        }
        $userId = Auth::id();

        // Messages in trash for the user = (their delete flag is set) AND (their permanent delete flag IS NULL)
        return Message::query()
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->whereNotNull('receiver_deleted_at')
                    ->whereNull('receiver_permanently_deleted_at'); // New condition
            })->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->whereNotNull('sender_deleted_at')
                    ->whereNull('sender_permanently_deleted_at');   // New condition
            })
            ->with(['sender.additionalInfo', 'receiver.additionalInfo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function restoreMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if (!$message) {
            $this->dispatch('userMessageActionFeedback', message: __('Message not found.'), type: 'error');
            return;
        }

        $currentUserId = Auth::id();
        $restored = false;

        if ($message->receiver_id === $currentUserId && $message->receiver_deleted_at) {
            $message->receiver_deleted_at = null;
            $message->receiver_archived_at = null; // Also unarchive
            $message->receiver_permanently_deleted_at = null; // Clear this flag too if set
            $restored = true;
        } elseif ($message->sender_id === $currentUserId && $message->sender_deleted_at) {
            $message->sender_deleted_at = null;
            $message->sender_archived_at = null; // Also unarchive
            $message->sender_permanently_deleted_at = null; // Clear this flag too
            $restored = true;
        }

        if ($restored) {
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message restored.'), type: 'status');
            $this->dispatch('messageRestored');
        }
    }

    public function deletePermanently(int $messageId): void
    {
        $message = Message::find($messageId);
        if (!$message) {
            $this->dispatch('userMessageActionFeedback', message: __('Message not found.'), type: 'error');
            return;
        }

        $currentUserId = Auth::id();
        $processed = false;

        // Set the new "permanently_deleted_at" flag for the current user
        // The original "deleted_at" (trash) flag remains set.
        if ($message->receiver_id === $currentUserId && $message->receiver_deleted_at && is_null($message->receiver_permanently_deleted_at)) {
            $message->receiver_permanently_deleted_at = now();
            $processed = true;
        } elseif ($message->sender_id === $currentUserId && $message->sender_deleted_at && is_null($message->sender_permanently_deleted_at)) {
            $message->sender_permanently_deleted_at = now();
            $processed = true;
        }

        if ($processed) {
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message permanently deleted.'), type: 'status');
            // This event will cause this component to re-render, and the query will now exclude this message.
            $this->dispatch('messagePermanentlyDeletedFromTrash');
        } else {
            // E.g., trying to permanently delete something not in their trash or already marked so
            $this->dispatch('userMessageActionFeedback', message: __('Could not perform action. Message may already be permanently deleted or not in your trash.'), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.mail.trash-box', [
            'messages' => $this->messages,
        ]);
    }
}