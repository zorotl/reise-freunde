<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination; // Use pagination
use Livewire\Attributes\Title;

#[Title('Followers')]
class FollowersList extends Component
{
    use WithPagination;

    public User $user;

    public function mount(int $id)
    {
        $this->user = User::with('additionalInfo')->findOrFail($id);
    }

    public function render()
    {
        $followers = $this->user
            ->followers() // Get the query builder
            ->with('additionalInfo') // Eager load info for display
            ->paginate(15); // Paginate results

        return view('livewire.user.followers-list', [
            'followers' => $followers,
        ]);
    }
}