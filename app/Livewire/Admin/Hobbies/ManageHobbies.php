<?php

namespace App\Livewire\Admin\Hobbies;

use App\Models\Hobby; // Import the Hobby model
use Livewire\Component;
use Livewire\WithPagination; // Trait for pagination (optional for small lists)

class ManageHobbies extends Component
{
    use WithPagination; // Use pagination trait

    public $search = ''; // Property for search input (hobby name)
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    // Properties for adding/editing
    public $editingHobbyId = null; // ID of the hobby being edited
    public $name = ''; // Name of the hobby for form

    // Listeners for events (for refreshing after actions)
    protected $listeners = [
        'hobbyUpdated' => '$refresh',
        'hobbyAdded' => '$refresh',
        'hobbyDeleted' => '$refresh',
        'hobbyRestored' => '$refresh',
    ];

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Set the sort field and direction
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    // Start adding or editing
    public function addHobby()
    {
        $this->editingHobbyId = null; // Clear any editing state
        $this->name = ''; // Reset form field
    }

    public function editHobby($hobbyId)
    {
        $hobby = Hobby::withTrashed()->find($hobbyId); // Find hobby including soft deleted
        if ($hobby) {
            $this->editingHobbyId = $hobby->id;
            $this->name = $hobby->name;
        } else {
            session()->flash('error', 'Hobby not found.');
            $this->cancelEdit();
        }
    }

    // Cancel adding or editing
    public function cancelEdit()
    {
        $this->reset(['editingHobbyId', 'name']); // Reset form state
        $this->resetValidation(); // Clear validation errors
    }


    // Save or Update Hobby
    public function saveHobby()
    {
        $rules = ['name' => 'required|string|max:255|unique:hobbies,name']; // Base rule for unique name

        // If editing, the name must be unique EXCEPT for the current hobby
        if ($this->editingHobbyId) {
            $rules['name'] .= ',' . $this->editingHobbyId;
        }

        $this->validate($rules); // Run validation

        if ($this->editingHobbyId) {
            // Update existing hobby
            $hobby = Hobby::withTrashed()->find($this->editingHobbyId);
            if ($hobby) {
                $hobby->name = $this->name;
                $hobby->save();
                session()->flash('message', 'Hobby updated successfully.');
                $this->dispatch('hobbyUpdated');
            } else {
                session()->flash('error', 'Hobby not found for update.');
            }
        } else {
            // Create new hobby
            Hobby::create(['name' => $this->name]);
            session()->flash('message', 'Hobby added successfully.');
            $this->dispatch('hobbyAdded');
        }

        $this->cancelEdit(); // Close form
    }

    // --- Action Methods ---

    // Method to soft delete a hobby
    public function softDeleteHobby($hobbyId)
    {
        $hobby = Hobby::find($hobbyId); // Find only non-soft deleted
        if ($hobby) {
            $hobby->delete(); // Performs soft delete
            session()->flash('message', 'Hobby soft deleted successfully.');
            $this->dispatch('hobbyDeleted');
        } else {
            session()->flash('error', 'Hobby not found for soft delete.');
        }
    }

    // Method to restore a soft deleted hobby
    public function restoreHobby($hobbyId)
    {
        $hobby = Hobby::withTrashed()->find($hobbyId); // Find hobby including soft deleted ones
        if ($hobby) {
            $hobby->restore(); // Restore
            session()->flash('message', 'Hobby restored successfully.');
            $this->dispatch('hobbyRestored');
        } else {
            session()->flash('error', 'Hobby not found for restore.');
        }
    }

    // Method to force delete a hobby
    public function forceDeleteHobby($hobbyId)
    {
        $hobby = Hobby::withTrashed()->find($hobbyId); // Find hobby including soft deleted ones
        if ($hobby) {
            $hobby->forceDelete(); // Permanently delete
            session()->flash('message', 'Hobby permanently deleted.');
            $this->dispatch('hobbyDeleted');
        } else {
            session()->flash('error', 'Hobby not found for force delete.');
        }
    }


    // Render the component
    public function render()
    {
        // Fetch hobbies, include soft deleted
        $hobbies = Hobby::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->withTrashed() // Include soft deleted hobbies
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.hobbies.manage-hobbies', [ // Corrected view path
            'hobbies' => $hobbies,
        ]);
    }
}