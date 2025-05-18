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
    #[On('messagePermanentlyDeletedFromTrash')] // Event for permanent delete
    #[On('userMessageActionFeedback')] // Listener for general feedback
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

        // Messages that are "in the trash" for the current user
        // These are messages where their specific delete flag is set.
        // We also ensure they are not system-level soft-deleted by an admin unless we want to show those here.
        // For now, let's assume admin soft-deleted messages are fully hidden from user trash.
        return Message::query()
            ->where(function ($query) use ($userId) {
                // Messages received by the user AND "deleted" (in trash) by them
                $query->where('receiver_id', $userId)
                    ->whereNotNull('receiver_deleted_at');
            })->orWhere(function ($query) use ($userId) {
                // Messages sent by the user AND "deleted" (in trash) by them
                $query->where('sender_id', $userId)
                    ->whereNotNull('sender_deleted_at');
            })
            ->with(['sender.additionalInfo', 'receiver.additionalInfo']) // Eager load
            ->orderBy('created_at', 'desc') // Or order by deletion date if preferred
            ->paginate(10);
    }

    public function restoreMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if (!$message)
            return;

        $currentUserId = Auth::id();
        $restored = false;

        if ($message->receiver_id === $currentUserId && $message->receiver_deleted_at) {
            $message->receiver_deleted_at = null;
            // Also unarchive if it was archived, to restore to inbox/outbox cleanly
            if ($message->receiver_archived_at) {
                $message->receiver_archived_at = null;
            }
            $restored = true;
        } elseif ($message->sender_id === $currentUserId && $message->sender_deleted_at) {
            $message->sender_deleted_at = null;
            // Also unarchive
            if ($message->sender_archived_at) {
                $message->sender_archived_at = null;
            }
            $restored = true;
        }

        if ($restored) {
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message restored.'), type: 'status');
            $this->dispatch('messageRestored'); // To refresh this list and potentially others
        }
    }

    public function deletePermanently(int $messageId): void
    {
        $message = Message::find($messageId);
        if (!$message)
            return;

        $currentUserId = Auth::id();
        $processed = false;

        // To make it disappear from TrashBox (which lists based on *_deleted_at IS NOT NULL),
        // we must set the relevant *_deleted_at to NULL. This is effectively a "restore" action
        // if we consider "permanent delete from trash" to mean "I don't want to see this in my trash anymore".
        if ($message->receiver_id === $currentUserId && $message->receiver_deleted_at) {
            $message->receiver_deleted_at = null; // "Restore" it to make it disappear from trash
            // If it was also archived, that archive flag remains, it will go to archive.
            // Or, if we want "permanent delete from trash" to also unarchive:
            // $message->receiver_archived_at = null;
            $processed = true;
        } elseif ($message->sender_id === $currentUserId && $message->sender_deleted_at) {
            $message->sender_deleted_at = null; // "Restore" it
            // if ($message->sender_archived_at) {
            //     $message->sender_archived_at = null;
            // }
            $processed = true;
        }

        if ($processed) {
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message removed from trash.'), type: 'status'); // Changed message
            $this->dispatch('messagePermanentlyDeletedFromTrash'); // Event name is fine
        }
    }

    public function render()
    {
        return view('livewire.mail.trash-box', [
            'messages' => $this->messages,
        ]);
    }
}