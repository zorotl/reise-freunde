<?php

namespace App\Livewire\Parts;

use App\Models\User; // Import User model
use Illuminate\Support\Collection; // Import Collection
use Livewire\Component;

class SuggestedUsersSection extends Component
{
    // Property to receive data from the parent component (Overview)
    public Collection $suggestedUsers;

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will use the public property $suggestedUsers
        return view('livewire.parts.suggested-users-section');
    }

    // The 'followUser' action is handled by the parent component (Overview)
    // because it affects data displayed in the parent (the suggested users list itself).
    // The wire:click="followUser(...)" in the view automatically calls the parent method.
    // No method definition is needed here for that action.
}