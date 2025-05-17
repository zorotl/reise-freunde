<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use App\Models\UserConfirmation;
use Carbon\Carbon;

class ConfirmationRequest extends Component
{
    public User $target;

    public function sendRequest()
    {
        $user = auth()->user();

        if ($user->id === $this->target->id) {
            return;
        }

        $existing = UserConfirmation::where('requester_id', $user->id)
            ->where('confirmer_id', $this->target->id)
            ->first();

        // Block if previously rejected
        if ($existing && $existing->status === 'rejected') {
            session()->flash('error', __('You already requested this confirmation and it was rejected.'));
            return;
        }

        // Block if already pending or accepted
        if ($existing) {
            session()->flash('error', __('Request already exists.'));
            return;
        }

        // Weekly limit (already implemented)
        $sentInLast7Days = UserConfirmation::where('requester_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($sentInLast7Days >= 3) {
            session()->flash('error', __('You can only request up to 3 confirmations per week.'));
            return;
        }

        UserConfirmation::create([
            'requester_id' => $user->id,
            'confirmer_id' => $this->target->id,
        ]);

        $this->target->notify(new \App\Notifications\RealWorldConfirmationRequested($user));

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
