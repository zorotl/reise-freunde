<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\UserGrant;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ManageUsers extends Component
{
    use WithPagination; // Use the pagination trait

    #[Url(as: 'user', history: false)] // Sync with query string 'user'
    public $filterUserId = null; // <-- Add Url attribute

    // public $search = '';
    #[Url(as: 'q', history: false)] // Sync with query string 'q'
    public $search = ''; // Add Url attribute for search too

    #[Url(as: 'sort', history: false)] // Sync with query string 'sort'
    public $sortField = 'name'; // Add Url attribute

    #[Url(as: 'direction', history: false)] // Sync with query string 'direction'
    public $sortDirection = 'asc'; // Add Url attribute

    #[Url(as: 'perPage', history: false)] // Sync with query string 'perPage'
    public $perPage = 10; // Add Url attribute

    // Add or modify the mount method
    public function mount(Request $request) // <--- Inject Request
    {
        // Read the 'filterUserId' query string parameter
        if ($request->has('filterUserId')) { // <--- Changed 'user' to 'filterUserId'
            $this->filterUserId = (int) $request->query('filterUserId'); // <--- Changed 'user' to 'filterUserId'
        }
    }

    // Listeners for events (we might use these later for modals/updates)
    protected $listeners = [
        'userUpdated' => '$refresh', // Refresh the list when a user is updated
        'userDeleted' => '$refresh', // Refresh the list when a user is deleted
        'userRestored' => '$refresh', // Refresh the list when a user is restored
        // Maybe listen for an event from messages page to set filter? Or use query string.
        // #[On('filterUsers')] // Alternative using attribute syntax
        // public function filterUsers($userId) { $this->filterUserId = $userId; }
    ];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // You might want to reset page when filterUserId changes too, or use query string
    // public function updatedFilterUserId()
    // {
    //     $this->resetPage();
    // }

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
        $users = User::with('grant', 'additionalInfo')
            // Apply user ID filter if set
            ->when($this->filterUserId, function (Builder $query, $userId) {
                $query->where('id', $userId);
            }) // <--- Add this when clause
            ->when($this->search, function (Builder $query) { // Add Builder type hint for clarity
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('additionalInfo', function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%');
                });
            })
            ->withTrashed()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

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

    // Add a method to clear the filter:
    public function clearFilter()
    {
        $this->filterUserId = null;
        $this->search = ''; // Clear search too
        $this->resetPage();
    }

    // Methods for editing, setting roles, banning will be added or use modals later

}