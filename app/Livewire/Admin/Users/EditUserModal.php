<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Import User model
use App\Models\UserGrant; // Import UserGrant model
use Livewire\Component;
use Livewire\Attributes\On; // Trait for listeners

class EditUserModal extends Component
{
    public $show = false; // Property to control modal visibility
    public $userId; // To store the ID of the user being edited

    // Properties to hold user data for the form
    public $name;
    public $email;
    public $is_admin;
    public $is_moderator;
    public $is_banned;
    public $is_banned_until;

    // Define validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        'is_banned' => 'boolean',
        // is_banned_until is required only if is_banned is true and not null
        'is_banned_until' => 'nullable|date|after_or_equal:today|required_if:is_banned,true',
    ];

    // Lifecycle hook to load user data when the modal is opened
    #[On('openEditModal')]
    public function openEditModal($userId)
    {
        $user = User::with('grant')->find($userId); // Find the user and eager load grant

        if (!$user) {
            // Handle case where user is not found (e.g., show error, close modal)
            session()->flash('error', 'User not found.');
            $this->closeModal();
            return;
        }

        // Populate properties with user data
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        // Populate grant-related properties, defaulting if grant doesn't exist
        $this->is_admin = $user->grant->is_admin ?? false;
        $this->is_moderator = $user->grant->is_moderator ?? false;
        $this->is_banned = $user->grant->is_banned ?? false;
        $this->is_banned_until = $user->grant->is_banned_until ? $user->grant->is_banned_until->format('Y-m-d') : null; // Format date for input

        $this->show = true; // Show the modal
    }

    // Save the user changes
    public function saveUser()
    {
        $this->validate(); // Run validation

        $user = User::find($this->userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            $this->closeModal();
            return;
        }

        // Update user's main info
        $user->name = $this->name;
        $user->email = $this->email;
        // Do not update password here - should be a separate action
        $user->save();

        // Update or create UserGrant
        // Ensure a grant exists before trying to update/create it
        $grant = UserGrant::firstOrNew(['user_id' => $user->id]);

        $grant->is_admin = $this->is_admin;
        $grant->is_moderator = $this->is_moderator;
        $grant->is_banned = $this->is_banned;
        // Set is_banned_until only if is_banned is true, otherwise set to null
        $grant->is_banned_until = $this->is_banned ? $this->is_banned_until : null;

        $grant->save();

        session()->flash('message', 'User updated successfully.');

        $this->dispatch('userUpdated'); // Dispatch event to refresh the user list
        $this->closeModal(); // Close the modal
    }

    // Close the modal and reset properties
    public function closeModal()
    {
        $this->show = false;
        $this->reset([
            'userId',
            'name',
            'email',
            'is_admin',
            'is_moderator',
            'is_banned',
            'is_banned_until',
        ]); // Reset all properties
        $this->resetValidation(); // Clear validation errors
    }

    // Render the modal view
    public function render()
    {
        return view('livewire.admin.users.edit-user-modal');
    }
}