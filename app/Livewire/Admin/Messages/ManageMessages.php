<?php

namespace App\Livewire\Admin\Messages;

use App\Models\Message;
use App\Models\User;
use App\Models\UserGrant;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Carbon\Carbon;

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

    // Computed property to check if the current user is admin or moderator
    #[Computed()]
    public function isAdminOrModerator()
    {
        // Assuming auth() returns the current user and they have grant relationship
        return auth()->check() && auth()->user()->isAdminOrModerator(); // Using the method on User model
    }

    // --- Corrected Ban Sender Action ---
    public function banSender($senderId)
    {
        // Ensure the current user is authorized before performing the action
        if (!$this->isAdminOrModerator) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        $sender = User::with('grant')->find($senderId); // Eager load grant

        if (!$sender) {
            session()->flash('error', 'Sender user not found.');
            return;
        }

        // Prevent banning admin/moderator (optional safety)
        if ($sender->isAdminOrModerator()) {
            session()->flash('error', 'Cannot ban an admin or moderator via this action.');
            return;
        }

        // Find or create the user grant record
        $grant = $sender->grant ?? new UserGrant(['user_id' => $sender->id]);

        // Check if the user is already banned via grant       
        if ($grant->is_banned) {
            session()->flash('error', 'Sender user is already banned.');
            return;
        }

        $grant->is_banned = true;
        $grant->is_banned_until = null; // Set to null for permanent ban via this button

        $grant->save();

        session()->flash('message', 'Sender user banned successfully.');
        $this->dispatch('senderBanned');
    }
}