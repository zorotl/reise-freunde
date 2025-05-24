<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Followable;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination; // Add pagination

class FollowersList extends Component
{
    use Followable, WithPagination; // Use trait and pagination

    public User $user;

    public function mount(int $id): void
    {
        $this->user = User::findOrFail($id);
    }

    /**
     * Listen for the event and refresh the user data by reloading the relationship.
     */
    #[On('userFollowStateChanged')]
    public function refreshFollowersData(int $userId): void
    {
        $this->user->load('followers'); // Re-fetch followers
        // Reset pagination if needed, though usually re-render handles it.
        $this->resetPage();
    }

    public function render()
    {
        $followers = $this->user->followers()->paginate(15); // Use pagination

        return view('livewire.user.followers-list', [
            'followers' => $followers,
        ])->layout('components.layouts.app');
    }
}