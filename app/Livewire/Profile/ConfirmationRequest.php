<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use App\Models\UserConfirmation;

class ConfirmationRequest extends Component
{
    public User $target;

    public function sendRequest()
    {
        if (auth()->id() === $this->target->id) {
            return;
        }

        if (
            UserConfirmation::where('requester_id', auth()->id())
                ->where('confirmer_id', $this->target->id)->exists()
        ) {
            return;
        }

        UserConfirmation::create([
            'requester_id' => auth()->id(),
            'confirmer_id' => $this->target->id,
        ]);

        session()->flash('success', __('Confirmation request sent.'));
    }

    public function render()
    {
        $exists = UserConfirmation::where('requester_id', auth()->id())
            ->where('confirmer_id', $this->target->id)->first();

        return view('livewire.profile.confirmation-request', [
            'request' => $exists,
        ]);
    }
}
