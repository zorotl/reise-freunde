<?php

namespace App\Livewire\Pinboard;

use Livewire\Component;
use App\Models\PinboardEntry;
use Illuminate\Support\Carbon;

class ShowPinboard extends Component
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
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', Carbon::now());
            })
            ->latest()
            ->get();
    }
    public function render()
    {
        return view('livewire.pinboard.show-pinboard');
    }
}
