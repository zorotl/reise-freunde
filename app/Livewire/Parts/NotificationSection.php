<?php

namespace App\Livewire\Parts;

use App\Models\User; // Import User model
use Illuminate\Support\Collection; // Import Collection
use Livewire\Component;

class NotificationSection extends Component
{
    // Properties to receive data from the parent component (Overview)
    // These properties are public so Livewire can hydrate them.
    public Collection $pendingRequests;
    public User $user; // Receive the authenticated user

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will use the public properties $pendingRequests and $user
        return view('livewire.parts.notification-section');
    }

    // Add methods here if this component needs to handle actions (e.g., accept/decline requests directly)
    // Currently, the "View" link goes to a separate page, so no actions are needed here.
}