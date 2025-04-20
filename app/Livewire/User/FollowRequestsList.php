<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\WithPagination;

class FollowRequestsList extends Component
{
    use WithPagination;

    public ?User $user;

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user) {
            // Redirect or handle unauthorized access
            return redirect()->route('login');
        }
    }

    public function acceptRequest(int $requesterId)
    {
        $requester = User::find($requesterId);
        if ($requester && $this->user) {
            $accepted = $this->user->acceptFollowRequest($requester);
            if ($accepted) {
                session()->flash('message', 'You accepted the follow request from ' . $requester->name);
                // TODO: Send notification to $requester
                // $requester->notify(new \App\Notifications\FollowRequestAcceptedNotification($this->user));
            } else {
                session()->flash('error', 'Could not accept the request.');
            }
            // Refresh list
            $this->resetPage(); // Go back to page 1 after action
        }
    }

    public function declineRequest(int $requesterId)
    {
        $requester = User::find($requesterId);
        if ($requester && $this->user) {
            $declined = $this->user->declineFollowRequest($requester);
            if ($declined) {
                session()->flash('message', 'You declined the follow request from ' . $requester->name);
            } else {
                session()->flash('error', 'Could not decline the request.');
            }
            // Refresh list
            $this->resetPage(); // Go back to page 1 after action
        }
    }

    public function render()
    {
        if (!$this->user) {
            // Should not happen if mount redirects, but good practice
            return view('livewire.user.follow-requests-list', ['requests' => \Illuminate\Pagination\LengthAwarePaginator::make([], 0, 15)]);
        }

        $requests = $this->user
            ->pendingFollowerRequests() // Get pending requests TO this user
            ->with('additionalInfo')
            ->paginate(15);

        return view('livewire.user.follow-requests-list', [
            'requests' => $requests,
        ]);
    }
}