<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\On; // Import On attribute

#[Title('View Message')]
class MessageView extends Component
{
    public Message $message;
    public string $fromWhere = 'inbox'; // 'inbox' or 'outbox' or 'archive'

    #[On('messageMarkedRead')] // Listen for an event if needed
    public function refreshMessageState()
    {
        $this->message->refresh(); // Refresh model data
    }

    public function mount(Message $message, string $fromWhere = 'inbox')
    {
        $currentUserId = Auth::id();

        // Ensure user can view this message
        if ($message->receiver_id !== $currentUserId && $message->sender_id !== $currentUserId) {
            session()->flash('error', __('Unauthorized access to message.'));
            return $this->redirectRoute('mail.inbox', navigate: true);
        }

        // Check if message is deleted or archived by the current user for the current view context
        if ($fromWhere === 'inbox' && ($message->receiver_deleted_at || $message->receiver_archived_at)) {
            session()->flash('error', __('This message is no longer in your inbox.'));
            return $this->redirectRoute('mail.inbox', navigate: true);
        } elseif ($fromWhere === 'outbox' && ($message->sender_deleted_at || $message->sender_archived_at)) {
            session()->flash('error', __('This message is no longer in your outbox.'));
            return $this->redirectRoute('mail.outbox', navigate: true);
        } elseif ($fromWhere === 'archive') {
            $isReceiverArchived = $message->receiver_id === $currentUserId && $message->receiver_archived_at;
            $isSenderArchived = $message->sender_id === $currentUserId && $message->sender_archived_at;
            if (!($isReceiverArchived || $isSenderArchived) || $message->isDeletedByCurrentUser()) {
                session()->flash('error', __('This message is not in your archive or has been deleted.'));
                return $this->redirectRoute('mail.archive', navigate: true);
            }
        }


        $this->message = $message->load(['sender.additionalInfo', 'receiver.additionalInfo']);
        $this->fromWhere = $fromWhere;

        // Mark as read if it's for the receiver, unread, and they are viewing it from inbox/archive
        if (in_array($fromWhere, ['inbox', 'archive']) && $message->receiver_id === $currentUserId && !$message->read_at) {
            $message->update(['read_at' => now()]);
            $this->dispatch('messageRead'); // Dispatch for unread count update
        }
    }

    public function archiveMessage(): void
    {
        $currentUserId = Auth::id();
        if ($this->message->receiver_id === $currentUserId) {
            $this->message->receiver_archived_at = now();
        } elseif ($this->message->sender_id === $currentUserId) {
            $this->message->sender_archived_at = now();
        } else {
            return; // Should not happen due to mount checks
        }
        $this->message->save();
        session()->flash('status', __('Message archived.'));
        $this->redirectToList();
    }

    public function unarchiveMessage(): void
    {
        $currentUserId = Auth::id();
        if ($this->message->receiver_id === $currentUserId && $this->message->receiver_archived_at) {
            $this->message->receiver_archived_at = null;
        } elseif ($this->message->sender_id === $currentUserId && $this->message->sender_archived_at) {
            $this->message->sender_archived_at = null;
        } else {
            return;
        }
        $this->message->save();
        session()->flash('status', __('Message unarchived.'));
        // Decide where to redirect. If unarchived from 'archive' view, maybe go to inbox/outbox?
        // For now, redirecting back to the archive which will now exclude it.
        // A better UX might be to redirect to inbox/outbox.
        $this->fromWhere = $this->message->sender_id === $currentUserId ? 'outbox' : 'inbox';
        $this->redirectToList();
    }


    public function deleteMessage(): void
    {
        $currentUserId = Auth::id();
        if ($this->fromWhere === 'archive') { // Deleting from archive
            if ($this->message->receiver_id === $currentUserId && $this->message->receiver_archived_at) {
                $this->message->receiver_deleted_at = now();
            } elseif ($this->message->sender_id === $currentUserId && $this->message->sender_archived_at) {
                $this->message->sender_deleted_at = now();
            }
        } else { // Deleting from inbox/outbox
            if ($this->message->receiver_id === $currentUserId) {
                $this->message->receiver_deleted_at = now();
            } elseif ($this->message->sender_id === $currentUserId) {
                $this->message->sender_deleted_at = now();
            } else {
                return; // Should not happen
            }
        }

        $this->message->save();
        session()->flash('status', __('Message deleted.'));
        $this->redirectToList();
    }

    protected function redirectToList(): void
    {
        $redirectRoute = match ($this->fromWhere) {
            'inbox' => 'mail.inbox',
            'outbox' => 'mail.outbox',
            'archive' => 'mail.archive',
            default => 'mail.inbox',
        };
        $this->dispatch('messageUpdated'); // Generic event to refresh lists
        $this->redirectRoute($redirectRoute, navigate: true);
    }

    public function render()
    {
        return view('livewire.mail.message-view');
    }
}