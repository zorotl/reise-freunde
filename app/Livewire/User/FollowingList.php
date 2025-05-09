<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Following')]
class FollowingList extends Component
{
    use WithPagination;

    public User $user;
    public ?User $loggedInUser;

    public function mount(int $id)
    {
        $this->user = User::with('additionalInfo')->findOrFail($id);
        $this->loggedInUser = Auth::user(); // Needed for unfollow button
    }

    public function unfollow(int $userIdToUnfollow)
    {
        if (!$this->loggedInUser || $this->loggedInUser->id !== $this->user->id) {
            // Only allow unfollowing if viewing your own following list
            session()->flash('error', 'You can only unfollow users from your own list.');
            return;
        }

        $userToUnfollow = User::find($userIdToUnfollow);
        if ($userToUnfollow) {
            $this->loggedInUser->unfollow($userToUnfollow);
            // Optionally force refresh the component data - pagination might reset
            // $this->resetPage();
            session()->flash('message', 'You have unfollowed ' . $userToUnfollow->name);
        }
    }


    public function render()
    {
        $following = $this->user
            ->following() // Get the query builder for accepted following
            ->with('additionalInfo')
            ->paginate(15);

        return view('livewire.user.following-list', [
            'following' => $following,
        ]);
    }
}