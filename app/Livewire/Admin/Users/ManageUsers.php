<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Import the User model
use Livewire\Component;
use Livewire\WithPagination; // Trait for pagination

class ManageUsers extends Component
{
    use WithPagination; // Use the pagination trait

    public $search = ''; // Property for search input
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    // Listeners for events (we might use these later for modals/updates)
    protected $listeners = [
        'userUpdated' => '$refresh', // Refresh the list when a user is updated
        'userDeleted' => '$refresh', // Refresh the list when a user is deleted
        'userRestored' => '$refresh', // Refresh the list when a user is restored
    ];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Set the sort field and direction
    public function sortBy($field)
    {
        // If sorting by the same field, reverse the direction
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Otherwise, set the new field and default to ascending
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    // Render the component
    public function render()
    {
        // Fetch users with their grants and additional info
        $users = User::with('grant', 'additionalInfo') // Eager load relationships
            ->when($this->search, function ($query) {
                // Apply search filter to name, email, or username
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    // Search on username from UserAdditionalInfo
                    ->orWhereHas('additionalInfo', function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%');
                });
            })
            // Include soft deleted users
            ->withTrashed()
            ->orderBy($this->sortField, $this->sortDirection) // Apply sorting
            ->paginate($this->perPage); // Apply pagination

        return view('livewire.admin.users.manage-users', [
            'users' => $users,
        ]);
    }

    // --- Basic Action ---

    // Method to soft delete a user
    public function softDeleteUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete(); // This performs a soft delete because SoftDeletes trait is used
            session()->flash('message', 'User soft deleted successfully.');
            $this->dispatch('userDeleted'); // Dispatch event to refresh list
        }
    }

    // Method to restore a soft deleted user
    public function restoreUser($userId)
    {
        $user = User::withTrashed()->find($userId); // Find user including soft deleted ones
        if ($user) {
            $user->restore(); // Restore the user
            session()->flash('message', 'User restored successfully.');
            $this->dispatch('userRestored'); // Dispatch event to refresh list
        }
    }

    // Method to force delete a user
    public function forceDeleteUser($userId)
    {
        $user = User::withTrashed()->find($userId); // Find user including soft deleted ones
        if ($user) {
            $user->forceDelete(); // Permanently delete the user
            session()->flash('message', 'User permanently deleted.');
            $this->dispatch('userDeleted'); // Dispatch event to refresh list
        }
    }


    // Methods for editing, setting roles, banning will be added or use modals later

}