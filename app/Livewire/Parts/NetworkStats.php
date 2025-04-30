<?php

namespace App\Livewire\Parts;

use App\Models\User; // Import User model
use Livewire\Component;

class NetworkStats extends Component
{
    // Properties to receive data from the parent component (Overview)
    public int $followerCount;
    public int $followingCount;
    public User $user; // Receive the authenticated user (needed for link URLs)

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will use the public properties $followerCount, $followingCount, and $user
        return view('livewire.parts.network-stats');
    }

    // This component is purely for display and linking, no Livewire actions are needed here.
}