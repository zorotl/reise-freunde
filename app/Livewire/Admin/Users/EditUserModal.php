<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\UserGrant; // Import UserGrant model
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon; // Import Carbon for date handling

class EditUserModal extends Component
{
    public $show = false;
    public $userId;
    public $name;
    public $email;
    public $is_admin;
    public $is_moderator;

    // Properties for ban management
    public $is_banned = false; // Initialize to false
    public $banned_until; // Property for the banned_until date
    public $banned_reason; // Property for the banned_reason text

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        // Validation for ban fields
        'is_banned' => 'boolean',
        'banned_until' => 'nullable|date|after_or_equal:today',
        'banned_reason' => 'nullable|string|max:1000',
    ];

    // Listen for the openEditModal event
    #[On('openEditModal')]
    public function openEditModal($userId)
    {
        $user = User::with('grant')->find($userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            $this->closeModal();
            return;
        }

        // Populate existing properties
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        // Default roles to false if grant relationship is null
        $this->is_admin = $user->grant->is_admin ?? false;
        $this->is_moderator = $user->grant->is_moderator ?? false;

        // Populate ban properties from user grant, defaulting to false/null
        $this->is_banned = $user->grant->is_banned ?? false;
        // Format the date for the HTML date input (YYYY-MM-DD)
        $this->banned_until = $user->grant->is_banned_until ? $user->grant->is_banned_until->format('Y-m-d') : null;
        $this->banned_reason = $user->grant->banned_reason ?? null;

        $this->show = true; // Show the modal
    }

    // Save the user changes 
    public function saveUser()
    {
        // Adjust unique rule for email dynamically to ignore the current user being edited
        $this->rules['email'] .= '|unique:users,email,' . $this->userId;

        // Conditional validation for banned_until and banned_reason
        if ($this->is_banned) {
            $this->rules['banned_until'] = 'nullable|date|after_or_equal:today';
            $this->rules['banned_reason'] = 'nullable|string|max:1000';
        } else {
            $this->rules['banned_until'] = 'nullable';
            $this->rules['banned_reason'] = 'nullable';
        }


        $this->validate($this->rules); // Run validation

        // Reload the user with relationship to ensure we have the latest state before saving        
        $user = User::with('grant')->find($this->userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            $this->closeModal();
            return;
        }

        // Update basic user properties
        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        // Find the existing grant or create a new one if it doesn't exist
        $grant = $user->grant ?? new UserGrant(['user_id' => $user->id]);

        // Update grant properties, including role and ban status fields
        $grant->is_admin = $this->is_admin;
        $grant->is_moderator = $this->is_moderator;
        $grant->is_banned = $this->is_banned;

        // Set banned_until and banned_reason only if the user is marked as banned in the modal
        // If is_banned is false, clear banned_until and banned_reason in the database
        $grant->is_banned_until = $this->is_banned ? ($this->banned_until ? Carbon::parse($this->banned_until) : null) : null;
        $grant->banned_reason = $this->is_banned ? $this->banned_reason : null;

        $grant->save(); // Save the changes to the user_grants record

        session()->flash('message', 'User updated successfully.');

        // Dispatch event to notify other components (like ManageUsers) to refresh their data
        $this->dispatch('userUpdated');
        $this->closeModal(); // Close the modal after saving
    }

    // Close the modal and reset all properties
    public function closeModal()
    {
        $this->show = false;
        // Reset all properties to their initial states
        $this->reset([
            'userId',
            'name',
            'email',
            'is_admin',
            'is_moderator',
            'is_banned',
            'banned_until',
            'banned_reason'
        ]);
        $this->resetValidation(); // Clear any validation errors
    }

    // Render the modal view
    public function render()
    {
        return view('livewire.admin.users.edit-user-modal');
    }
}