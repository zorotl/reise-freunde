<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Title('Mail Inbox')]
class Inbox extends Component
{
    use WithPagination;

    public $unreadCount;
    protected $paginationTheme = 'tailwind';

    #[On('messageArchived')] // For self-refresh if needed
    #[On('messageDeleted')]  // For self-refresh if needed
    #[On('messageRead')]
    #[On('userMessageActionFeedback')] // Listener to re-flash for UI
    public function handleFeedback($message = null, $type = 'status'): void
    {
        if ($message) {
            session()->flash($type, $message);
        }
        $this->loadUnreadCount();
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function getMessagesProperty()
    {
        // ... (existing logic) ...
        if (!Auth::check()) {
            return Message::query()->paginate(10);
        }
        return Message::where('receiver_id', Auth::id())
            ->whereNull('receiver_deleted_at')
            ->whereNull('receiver_archived_at')
            ->select(['id', 'sender_id', 'subject', 'created_at', 'read_at']) // SELECT only what's needed for the list
            ->with(['sender:id,firstname,lastname', 'sender.additionalInfo:user_id,username']) // Eager load specific columns
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function loadUnreadCount()
    {
        // ... (existing logic) ...
        if (Auth::check()) {
            $this->unreadCount = Message::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->whereNull('receiver_deleted_at')
                ->whereNull('receiver_archived_at')
                ->count();
        } else {
            $this->unreadCount = 0;
        }
    }

    public function archiveMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id === Auth::id()) {
            $message->receiver_archived_at = now();
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message archived.'), type: 'status');
            $this->dispatch('messageArchived'); // For other components or self-refresh if not using the feedback listener for that
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id === Auth::id()) {
            $message->receiver_deleted_at = now();
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message deleted.'), type: 'status');
            $this->dispatch('messageDeleted');
        }
    }

    public function render()
    {
        return view('livewire.mail.inbox', [
            'messages' => $this->messages,
        ]);
    }
}