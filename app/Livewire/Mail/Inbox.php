<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Mail Inbox')]
class Inbox extends Component
{
    use WithPagination;

    public $unreadCount;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function getMessagesProperty()
    {
        return Message::where('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function loadUnreadCount()
    {
        $this->unreadCount = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead($messageId)
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id === Auth::id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
            $this->loadUnreadCount(); // Only update count; messages auto-refresh via pagination
        }
    }

    public function render()
    {
        return view('livewire.mail.inbox', [
            'messages' => $this->messages,
        ]);
    }
}
