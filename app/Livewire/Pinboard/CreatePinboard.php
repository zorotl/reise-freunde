<?php

namespace App\Livewire\Pinboard;

use Livewire\Component;
use App\Models\PinboardEntry;

class CreatePinboard extends Component
{
    public $title;
    public $content;
    public $expiryDate;
    public $entries;

    public function save()
    {
        $this->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'expiryDate' => 'required|date|after:today',
        ]);

        PinboardEntry::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'expiry_date' => $this->expiryDate,
        ]);

        session()->flash('success', 'New post successfully created.');
        $this->redirect('/pinboard/show', navigate: true);
    }
    public function render()
    {
        return view('livewire.pinboard.create-pinboard');
    }
}
