<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\UserConfirmation;
use App\Notifications\YouConfirmedSomeone;
use App\Notifications\RealWorldConfirmationAccepted;

#[Title('Confirm Real-World Confirmation Request')]
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
        $c->requester->notify(new RealWorldConfirmationAccepted(auth()->user())); // Trigger notification to requester
        auth()->user()->notify(new YouConfirmedSomeone($c->requester));
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
