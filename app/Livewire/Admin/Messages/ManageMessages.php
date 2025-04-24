<?php

namespace App\Livewire\Admin\Messages;

use App\Models\Message; // Import the Message model
use App\Models\User; // Import the User model (needed for banning sender)
use Livewire\Component;
use Livewire\WithPagination; // Trait for pagination

class ManageMessages extends Component
{
    use WithPagination; // Use the pagination trait

    public $search = ''; // Property for search input (subject, body, sender/receiver name/email)
    public $sortField = 'created_at'; // Default sort field
    public $sortDirection = 'desc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    // Listeners for events (for refreshing after actions)
    protected $listeners = [
        'senderBanned' => '$refresh', // Refresh the list when a sender is banned (user soft deleted)
        'messageDeleted' => '$refresh', // Refresh if we add message deletion later
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


    // Render the component
    public function render()
    {
        // Fetch messages, eager load sender and receiver relationships
        $messages = Message::with([
            'sender' => function ($query) {
                // Eager load sender's grant and additional info
                $query->withTrashed()->with('grant', 'additionalInfo');
            },
            'receiver' => function ($query) {
                // Eager load receiver's grant and additional info
                $query->withTrashed()->with('grant', 'additionalInfo');
            }
        ])
            ->when($this->search, function ($query) {
                // Apply search filter to subject, body, sender/receiver name/email
                $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%')
                    // Search on sender's name or email
                    ->orWhereHas('sender', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                    // Search on receiver's name or email
                    ->orWhereHas('receiver', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $q->search . '%'); // Corrected variable here
                });
            })
            ->orderBy($this->sortField, $this->sortDirection) // Apply sorting
            ->paginate($this->perPage); // Apply pagination

        return view('livewire.admin.messages.manage-messages', [
            'messages' => $messages,
        ]);
    }

    // --- Action Method: Ban Sender (Soft Delete User) ---

    // Method to ban the sender of a message (soft delete the user)
    public function banSender($senderId)
    {
        $sender = User::find($senderId);

        if (!$sender) {
            session()->flash('error', 'Sender user not found.');
            return;
        }

        // Prevent banning admin/moderator via this action (optional safety)
        if ($sender->isAdminOrModerator()) {
            session()->flash('error', 'Cannot ban an admin or moderator via this action.');
            return;
        }

        // Check if the user is already soft deleted
        if ($sender->trashed()) {
            session()->flash('message', 'Sender is already banned/soft deleted.');
            return;
        }

        // Perform the soft delete on the sender's User model
        $sender->delete(); // Requires SoftDeletes trait and deleted_at column on 'users' table

        // Optional: Add a record to user_grants indicating they were banned for a specific reason/duration
        // UserGrant::updateOrCreate(['user_id' => $sender->id], ['is_banned' => true, 'is_banned_until' => null, 'banned_reason' => 'Banned via message action']);


        session()->flash('message', 'Sender user banned successfully.');
        $this->dispatch('senderBanned'); // Dispatch event to refresh the list
    }

    // Note: We might add message soft delete/restore actions later if needed
}