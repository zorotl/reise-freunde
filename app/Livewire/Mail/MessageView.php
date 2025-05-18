<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Str; // For Str::startsWith

#[Title('View Message')]
class MessageView extends Component
{
    public Message $message; // Type-hinted model instance
    public string $fromWhere = 'inbox'; // 'inbox', 'outbox', or 'archive'

    #[On('userMessageActionFeedback')] // Listener for feedback
    public function handleFeedback($message = null, $type = 'status'): void
    {
        if ($message) {
            session()->flash($type, $message);
        }
        // Potentially refresh message state if needed, though redirect usually handles this
        $this->message->refresh();
    }

    #[On('messageRead')] // Can be dispatched by self for UI updates if needed
    public function refreshMessageDisplay()
    {
        $this->message->refresh();
    }


    public function mount(Message $message, string $fromWhere = 'inbox')
    {
        $currentUserId = Auth::id();

        if (!$currentUserId) { // Guest check
            session()->flash('error', __('You must be logged in to view messages.'));
            return $this->redirectRoute('login');
        }

        // Ensure user can view this message (is sender or receiver)
        if ($message->receiver_id !== $currentUserId && $message->sender_id !== $currentUserId) {
            session()->flash('error', __('Unauthorized access to message.'));
            return $this->redirectRoute('mail.inbox', navigate: true);
        }

        // Validate if the message should be accessible from the specified 'fromWhere' context
        $redirect = false;
        if ($fromWhere === 'inbox') {
            if ($message->receiver_id !== $currentUserId || $message->receiver_deleted_at || $message->receiver_archived_at) {
                session()->flash('error', __('This message is not accessible from your inbox.'));
                $redirect = true;
            }
        } elseif ($fromWhere === 'outbox') {
            if ($message->sender_id !== $currentUserId || $message->sender_deleted_at || $message->sender_archived_at) {
                session()->flash('error', __('This message is not accessible from your outbox.'));
                $redirect = true;
            }
        } elseif ($fromWhere === 'archive') {
            $isReceiverArchived = $message->receiver_id === $currentUserId && $message->receiver_archived_at;
            $isSenderArchived = $message->sender_id === $currentUserId && $message->sender_archived_at;
            if (!($isReceiverArchived || $isSenderArchived) || ($message->isDeletedByCurrentUser())) {
                session()->flash('error', __('This message is not accessible from your archive.'));
                $this->redirectRoute('mail.archive', navigate: true);
                return; // Added return
            }
        } else { // Default or invalid fromWhere, redirect to inbox
            session()->flash('error', __('Invalid message context.'));
            $redirect = true;
        }

        if ($redirect) {
            return $this->redirectRoute(($fromWhere === 'outbox' ? 'mail.outbox' : 'mail.inbox'), navigate: true);
        }

        $this->message = $message->load(['sender.additionalInfo', 'receiver.additionalInfo']);
        $this->fromWhere = $fromWhere;

        // Mark as read if it's for the receiver, unread, and they are viewing it from inbox/archive
        if (in_array($this->fromWhere, ['inbox', 'archive']) && $this->message->receiver_id === $currentUserId && !$this->message->read_at) {
            $this->message->update(['read_at' => now()]);
            $this->dispatch('messageRead'); // For unread count update elsewhere
        }
    }

    public function archiveMessage(): void
    {
        $currentUserId = Auth::id();
        $archived = false;
        if ($this->message->receiver_id === $currentUserId && !$this->message->receiver_archived_at) {
            $this->message->receiver_archived_at = now();
            $archived = true;
        } elseif ($this->message->sender_id === $currentUserId && !$this->message->sender_archived_at) {
            $this->message->sender_archived_at = now();
            $archived = true;
        }

        if ($archived) {
            $this->message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message archived.'), type: 'status');
            $this->redirectToList();
        }
    }

    public function unarchiveMessage(): void
    {
        $currentUserId = Auth::id();
        $unarchived = false;
        if ($this->message->receiver_id === $currentUserId && $this->message->receiver_archived_at) {
            $this->message->receiver_archived_at = null;
            $unarchived = true;
            $this->fromWhere = 'inbox'; // Set context for redirectToList
        } elseif ($this->message->sender_id === $currentUserId && $this->message->sender_archived_at) {
            $this->message->sender_archived_at = null;
            $unarchived = true;
            $this->fromWhere = 'outbox'; // Set context for redirectToList
        }

        if ($unarchived) {
            $this->message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message unarchived.'), type: 'status');
            $this->redirectToList();
        }
    }

    public function deleteMessage(): void
    {
        $currentUserId = Auth::id();
        $deleted = false;

        if ($this->message->receiver_id === $currentUserId) {
            $this->message->receiver_deleted_at = now();
            // If deleting from archive, also unarchive it so it doesn't show in archive list anymore
            if ($this->fromWhere === 'archive' && $this->message->receiver_archived_at) {
                $this->message->receiver_archived_at = null;
            }
            $deleted = true;
        } elseif ($this->message->sender_id === $currentUserId) {
            $this->message->sender_deleted_at = now();
            if ($this->fromWhere === 'archive' && $this->message->sender_archived_at) {
                $this->message->sender_archived_at = null;
            }
            $deleted = true;
        }

        if ($deleted) {
            $this->message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message deleted.'), type: 'status');
            $this->redirectToList();
        }
    }

    protected function redirectToList(): void
    {
        $redirectRoute = match ($this->fromWhere) {
            'inbox' => 'mail.inbox',
            'outbox' => 'mail.outbox',
            'archive' => 'mail.archive',
            default => 'mail.inbox', // Sensible default
        };
        // Dispatch a general event that lists can listen to for refresh
        // This is more generic than messageArchived, messageDeleted etc.
        $this->dispatch('mailBoxUpdated');
        $this->redirectRoute($redirectRoute, navigate: true);
    }

    public function render()
    {
        return view('livewire.mail.message-view');
    }
}