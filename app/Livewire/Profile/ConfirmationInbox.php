<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\UserConfirmation;
use Livewire\Attributes\Title;

#[Title('Confirm Bürgschaft Request')]
class ConfirmationInbox extends Component
{
    public $requests;

    public function mount()
    {
        $this->requests = auth()->user()
            ->confirmationsReceived()
            ->where('status', 'pending')
            ->with('requester')
            ->get();
    }

    public function accept($id)
    {
        $c = UserConfirmation::findOrFail($id);
        if ($c->confirmer_id !== auth()->id())
            return;

        $c->status = 'accepted';
        $c->save();
        $this->mount(); // refresh
    }

    public function reject($id)
    {
        $c = UserConfirmation::findOrFail($id);
        if ($c->confirmer_id !== auth()->id())
            return;

        $c->status = 'rejected';
        $c->save();
        $this->mount(); // refresh
    }

    public function render()
    {
        return view('livewire.profile.confirmation-inbox');
    }
}
