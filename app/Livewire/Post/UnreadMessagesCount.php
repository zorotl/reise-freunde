<?php

namespace App\Livewire\Post;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnreadMessagesCount extends Component
{
    public int $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount(): void
    {
        if (Auth::check()) {
            $this->count = Message::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->count();
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        return view('livewire.post.unread-messages-count');
    }
}