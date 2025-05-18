<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On; // Import On attribute

#[Title('Mail Outbox')]
class Outbox extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Listen for events to refresh the component
    #[On('messageArchived')]
    #[On('messageDeleted')]
    public function refreshComponent(): void
    {
        $this->resetPage();
    }

    public function getMessagesProperty()
    {
        if (!Auth::check()) {
            return Message::query()->paginate(10);
        }
        return Message::where('sender_id', Auth::id())
            ->whereNull('sender_deleted_at') // Only show non-deleted for sender
            ->whereNull('sender_archived_at') // Only show non-archived for sender
            ->with(['receiver.additionalInfo']) // Eager load receiver and their additionalInfo
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function archiveMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id === Auth::id()) {
            $message->sender_archived_at = now();
            $message->save();
            $this->dispatch('messageArchived');
            session()->flash('status', __('Message archived.'));
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id === Auth::id()) {
            $message->sender_deleted_at = now();
            $message->save();
            $this->dispatch('messageDeleted');
            session()->flash('status', __('Message deleted.'));
        }
    }

    public function render()
    {
        return view('livewire.mail.outbox', [
            'messages' => $this->messages, // Access the computed property
        ]);
    }
}