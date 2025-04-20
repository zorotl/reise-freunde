<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;

class MessageCompose extends Component
{

    public ?int $receiver_id = null;
    public ?bool $fixReceiver = false;
    #[Rule('required|string|max:255')]
    public string $subject = '';
    #[Rule('required|string')]
    public string $body = '';

    public string $search = '';
    public array $searchResults = [];
    public string $selectedRecipientName = '';

    public function mount(?int $receiverId = null, ?bool $fixReceiver = false)
    {
        if ($receiverId) {
            $recipient = User::find($receiverId);
            if ($recipient) {
                $this->receiver_id = $recipient->id;
                $this->selectedRecipientName = $recipient->name;
            }
        }
        $this->fixReceiver = $fixReceiver;
    }

    public function updatedSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->searchResults = User::where('name', 'like', '%' . $value . '%')
                ->where('id', '!=', Auth::id())
                ->get()
                ->toArray(); // Convert the Collection to an array            
        } else {
            $this->searchResults = [];
        }
        $this->receiver_id = null; // Reset selected recipient when searching
    }

    public function selectRecipient(User $user)
    {
        $this->receiver_id = $user->id;
        $this->selectedRecipientName = $user->name;
        $this->search = '';
        $this->searchResults = [];
    }

    public function deselectRecipient()
    {
        $this->receiver_id = null;
        $this->selectedRecipientName = '';
        $this->search = ''; // Optionally clear the search
        $this->showResults = false; // Optionally hide results
    }

    public function sendMessage()
    {
        $this->validate();

        if (!$this->receiver_id) {
            $this->addError('receiver_id', 'Please select a recipient.');
            return;
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiver_id,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        session()->flash('message', 'Message sent!');
        $this->reset(['receiver_id', 'subject', 'body', 'selectedRecipientName']);
        $this->dispatch('close-modal'); // Assuming you still use a modal for compose
    }

    public function render()
    {
        return view('livewire.mail.message-compose');
    }
}