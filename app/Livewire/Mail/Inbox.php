<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Attributes\On; // Import On attribute

#[Title('Mail Inbox')]
class Inbox extends Component
{
    use WithPagination;

    public $unreadCount;
    protected $paginationTheme = 'tailwind'; // Or your preferred theme

    // Listen for events to refresh the component
    #[On('messageArchived')]
    #[On('messageDeleted')]
    #[On('messageRead')] // If you dispatch this from MessageView
    public function refreshComponent(): void
    {
        $this->loadUnreadCount();
        $this->resetPage(); // To ensure pagination is correct after list changes
    }

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function getMessagesProperty()
    {
        if (!Auth::check()) {
            return Message::query()->paginate(10); // Return empty paginator for guests
        }
        return Message::where('receiver_id', Auth::id())
            ->whereNull('receiver_deleted_at') // Only show non-deleted by receiver
            ->whereNull('receiver_archived_at') // Only show non-archived by receiver
            ->with(['sender.additionalInfo']) // Eager load sender and their additionalInfo
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function loadUnreadCount()
    {
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
            $this->dispatch('messageArchived'); // Dispatch event
            session()->flash('status', __('Message archived.'));
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id === Auth::id()) {
            $message->receiver_deleted_at = now();
            $message->save();
            $this->dispatch('messageDeleted'); // Dispatch event
            session()->flash('status', __('Message deleted.'));
        }
    }

    public function render()
    {
        return view('livewire.mail.inbox', [
            'messages' => $this->messages, // Access the computed property
        ]);
    }
}