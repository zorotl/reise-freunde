<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Mail Outbox')]
class Outbox extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[On('messageArchived')]
    #[On('messageDeleted')]
    #[On('userMessageActionFeedback')] // Listener for feedback
    public function handleFeedback($message = null, $type = 'status'): void
    {
        if ($message) {
            session()->flash($type, $message);
        }
        $this->resetPage(); // Refresh the current page of results
    }

    public function getMessagesProperty()
    {
        if (!Auth::check()) {
            // Return an empty paginator or handle as per your app's logic for guests
            return Message::query()->whereRaw('1 = 0')->paginate(10);
        }
        return Message::where('sender_id', Auth::id())
            ->whereNull('sender_deleted_at')
            ->whereNull('sender_archived_at')
            ->select(['id', 'receiver_id', 'subject', 'created_at', 'read_at']) // SELECT specific columns
            ->with(['receiver:id,firstname,lastname', 'receiver.additionalInfo:user_id,username']) // Eager load specific columns
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function archiveMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id === Auth::id()) {
            $message->sender_archived_at = now();
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message archived.'), type: 'status');
            $this->dispatch('messageArchived'); // For other components or self-refresh
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id === Auth::id()) {
            $message->sender_deleted_at = now();
            $message->save();
            $this->dispatch('userMessageActionFeedback', message: __('Message deleted.'), type: 'status');
            $this->dispatch('messageDeleted'); // For other components or self-refresh
        }
    }

    public function render()
    {
        return view('livewire.mail.outbox', [
            'messages' => $this->messages,
        ]);
    }
}