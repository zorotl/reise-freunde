<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Followable;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination; // Add pagination

class FollowingList extends Component
{
    use Followable, WithPagination; // Use the trait and pagination

    public User $user;

    public function mount(int $id): void
    {
        $this->user = User::findOrFail($id);
    }

    /**
     * Listen for the event and refresh the user data by reloading the relationship.
     */
    #[On('userFollowStateChanged')]
    public function refreshFollowingData(int $userId): void
    {
        $this->user->load('following'); // Re-fetch following
        // Since unfollowing removes a user, we must re-render and paginate
        $this->resetPage();
    }

    /**
     * Remove the old 'unfollow' method if it exists.
     * The trait now provides 'unfollowUser'.
     */
    // public function unfollow(User $userToUnfollow) { ... } // DELETE THIS

    public function render()
    {
        $following = $this->user->following()->paginate(15); // Use pagination

        return view('livewire.user.following-list', [
            'following' => $following,
        ])->layout('components.layouts.app');
    }
}