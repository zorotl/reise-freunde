<?php

namespace App\Livewire\Admin\TravelStyles;

use App\Models\TravelStyle; // Import the TravelStyle model
use Livewire\Component;
use Livewire\WithPagination; // Trait for pagination (optional for small lists)
use Illuminate\Validation\Rule; // Import Rule for unique validation

class ManageTravelStyles extends Component
{
    use WithPagination; // Use pagination trait

    public $search = ''; // Property for search input (travel style name)
    public $sortField = 'name'; // Default sort field
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    // Properties for adding/editing
    public $editingTravelStyleId = null; // ID of the travel style being edited
    public $name = ''; // Name of the travel style for form

    // Listeners for events (for refreshing after actions)
    protected $listeners = [
        'travelStyleUpdated' => '$refresh',
        'travelStyleAdded' => '$refresh',
        'travelStyleDeleted' => '$refresh',
        'travelStyleRestored' => '$refresh',
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
    public function addTravelStyle()
    {
        $this->editingTravelStyleId = null; // Clear any editing state
        $this->name = ''; // Reset form field
        $this->resetValidation(); // Clear validation errors
    }

    public function editTravelStyle($travelStyleId)
    {
        $travelStyle = TravelStyle::withTrashed()->find($travelStyleId); // Find travel style including soft deleted
        if ($travelStyle) {
            $this->editingTravelStyleId = $travelStyle->id;
            $this->name = $travelStyle->name;
            $this->resetValidation(); // Clear validation errors
        } else {
            session()->flash('error', 'Travel Style not found.');
            $this->cancelEdit();
        }
    }

    // Cancel adding or editing
    public function cancelEdit()
    {
        $this->reset(['editingTravelStyleId', 'name']); // Reset form state
        $this->resetValidation(); // Clear validation errors
    }

    // Save or Update Travel Style
    public function saveTravelStyle()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique rule, ignoring the current item if editing
                Rule::unique('travel_styles', 'name')->ignore($this->editingTravelStyleId),
            ],
        ];

        $this->validate($rules); // Run validation

        if ($this->editingTravelStyleId) {
            // Update existing travel style
            $travelStyle = TravelStyle::withTrashed()->find($this->editingTravelStyleId);
            if ($travelStyle) {
                $travelStyle->name = $this->name;
                $travelStyle->save();
                session()->flash('message', 'Travel Style updated successfully.');
                $this->dispatch('travelStyleUpdated');
            } else {
                session()->flash('error', 'Travel Style not found for update.');
            }
        } else {
            // Create new travel style
            TravelStyle::create(['name' => $this->name]);
            session()->flash('message', 'Travel Style added successfully.');
            $this->dispatch('travelStyleAdded');
        }

        $this->cancelEdit(); // Close form
    }

    // --- Action Methods ---

    // Method to soft delete a travel style
    public function softDeleteTravelStyle($travelStyleId)
    {
        $travelStyle = TravelStyle::find($travelStyleId); // Find only non-soft deleted
        if ($travelStyle) {
            $travelStyle->delete(); // Performs soft delete
            session()->flash('message', 'Travel Style soft deleted successfully.');
            $this->dispatch('travelStyleDeleted');
        } else {
            session()->flash('error', 'Travel Style not found for soft delete.');
        }
    }

    // Method to restore a soft deleted travel style
    public function restoreTravelStyle($travelStyleId)
    {
        $travelStyle = TravelStyle::withTrashed()->find($travelStyleId); // Find including soft deleted ones
        if ($travelStyle) {
            $travelStyle->restore(); // Restore
            session()->flash('message', 'Travel Style restored successfully.');
            $this->dispatch('travelStyleRestored');
        } else {
            session()->flash('error', 'Travel Style not found for restore.');
        }
    }

    // Method to force delete a travel style
    public function forceDeleteTravelStyle($travelStyleId)
    {
        $travelStyle = TravelStyle::withTrashed()->find($travelStyleId); // Find including soft deleted ones
        if ($travelStyle) {
            $travelStyle->forceDelete(); // Permanently delete
            session()->flash('message', 'Travel Style permanently deleted.');
            $this->dispatch('travelStyleDeleted');
        } else {
            session()->flash('error', 'Travel Style not found for force delete.');
        }
    }

    // Render the component
    public function render()
    {
        // Fetch travel styles, include soft deleted
        $travelStyles = TravelStyle::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->withTrashed() // Include soft deleted travel styles
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.travel-styles.manage-travel-styles', [
            'travelStyles' => $travelStyles,
        ]);
    }
}