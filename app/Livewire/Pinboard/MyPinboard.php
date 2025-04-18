<?php

namespace App\Livewire\Pinboard;

use Livewire\Component;
use App\Models\PinboardEntry;
use Illuminate\Support\Carbon;

class MyPinboard extends Component
{
    public $newTitle;
    public $newEntry;
    public $expiryDate;
    public $entries;

    public function mount()
    {
        $this->loadEntries();
    }

    public function toggleActive(PinboardEntry $entry)
    {
        $entry->update(['is_active' => !$entry->is_active]);
        $this->loadEntries();
    }

    public function deleteEntry(PinboardEntry $entry)
    {
        $entry->delete(); // Soft delete
        $this->loadEntries();
    }

    private function loadEntries()
    {
        $this->entries = PinboardEntry::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }
    public function render()
    {
        return view('livewire.pinboard.my-pinboard');
    }
}
