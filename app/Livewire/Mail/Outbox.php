<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Mail Outbox')]
class Outbox extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function getMessagesProperty()
    {
        return Message::where('sender_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.mail.outbox', [
            'messages' => $this->messages, // triggers the computed property
        ]);
    }
}
