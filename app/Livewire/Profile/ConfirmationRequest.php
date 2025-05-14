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

        // Prevent sending to self
        if ($user->id === $this->target->id) {
            return;
        }

        // Prevent duplicate requests
        if (
            UserConfirmation::where('requester_id', $user->id)
                ->where('confirmer_id', $this->target->id)->exists()
        ) {
            return;
        }

        // ❗ Limit: 3 per 7 days
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
