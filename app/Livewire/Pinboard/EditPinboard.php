<?php

namespace App\Livewire\Pinboard;

use Livewire\Component;
use App\Models\PinboardEntry;

class EditPinboard extends Component
{
    public PinboardEntry $entry;
    public $title;
    public $content;
    public $expiryDate;

    public function mount(PinboardEntry $id)
    {
        $this->entry = $id;
        $this->title = $this->entry->title;
        $this->content = $this->entry->content;
        $this->expiryDate = $this->entry->expiry_date ? $this->entry->expiry_date->format('Y-m-d') : null;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'expiryDate' => 'required|date|after:today',
        ]);

        $this->entry->update([
            'title' => $this->title,
            'content' => $this->content,
            'expiry_date' => $this->expiryDate,
        ]);

        session()->flash('success', 'Post successfully updated.');
        $this->redirect('/pinboard/show', navigate: true);
    }

    public function render()
    {
        return view('livewire.pinboard.edit-pinboard');
    }
}
