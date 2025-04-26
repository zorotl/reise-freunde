<?php

namespace App\Livewire\Admin\Messages;

use App\Models\Message;
use App\Models\User;
use App\Models\UserGrant;
use Livewire\Component;
use Livewire\Attributes\Computed; // Import Computed attribute
use Carbon\Carbon; // Import Carbon

class ViewMessage extends Component
{
    public $messageId;

    // Method to load the message
    public function mount($messageId)
    {
        $this->messageId = $messageId;
    }

    // Use a computed property to fetch the message and eager load relationships
    #[Computed()]
    public function message()
    {
        return Message::with([
            'sender' => function ($query) {
                $query->withTrashed()->with('grant', 'additionalInfo');
            },
            'receiver' => function ($query) {
                $query->withTrashed()->with('grant', 'additionalInfo');
            }
        ])
            ->find($this->messageId);

    }

    // Computed property to check if the current user is admin or moderator
    #[Computed()]
    public function isAdminOrModerator()
    {
        // Assuming auth() returns the current user and they have grant relationship
        return auth()->check() && auth()->user()->isAdminOrModerator(); // Using the method on User model
    }

    // --- Ban Sender Action (Same logic as in ManageMessages) ---
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
        $this->dispatch('$refresh');
    }


    public function render()
    {
        // Check if message exists before rendering
        if (!$this->message) {
            return view('livewire.admin.messages.message-not-found'); // Create a simple view for not found
        }

        return view('livewire.admin.messages.view-message');
    }
}