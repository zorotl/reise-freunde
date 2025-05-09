<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('View Message')]
class MessageView extends Component
{
    public Message $message;
    public $fromWhere = '';

    public function mount(Message $message, $fromWhere = '')
    {
        if ($message->receiver_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $this->message = $message;

        if ($message->receiver_id === Auth::id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        $this->fromWhere = $fromWhere;
    }

    public function render()
    {
        return view('livewire.mail.message-view');
    }
}