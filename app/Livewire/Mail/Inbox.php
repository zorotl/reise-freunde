<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Mail Inbox')]
class Inbox extends Component
{
    public $messages;
    public $unreadCount;

    public function mount()
    {
        $this->loadMessages();
        $this->loadUnreadCount();
    }

    public function loadMessages()
    {
        $this->messages = Message::where('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        $this->loadUnreadCount(); // Update unread count after loading messages
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
            $this->loadMessages(); // Refresh the list and unread count
        }
    }

    public function render()
    {
        return view('livewire.mail.inbox');
    }
}