<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;

#[Title('Compose Message')]
class MessageCompose extends Component
{

    public ?int $receiver_id = null;
    public ?bool $fixReceiver = false;
    #[Rule('required|string|max:255')]
    public string $subject = '';
    #[Rule('required|string')]
    public string $body = '';
    public ?int $replyToId = null;

    public string $search = '';
    public array $searchResults = [];
    public string $selectedRecipientName = '';
    public bool $showResults = false; // Added to control dropdown visibility explicitly

    public function mount(?int $receiverId = null, ?bool $fixReceiver = false, ?int $replyToId = null)
    {
        $this->replyToId = $replyToId;

        if ($receiverId) {
            $recipient = User::with('additionalInfo')->find($receiverId);
            if ($recipient) {
                $this->receiver_id = $recipient->id;
                $this->selectedRecipientName = $recipient->additionalInfo?->username ?: ($recipient->firstname . ' ' . $recipient->lastname);
                $this->fixReceiver = (bool) $fixReceiver;
            }
        }

        if ($this->replyToId) {
            $original = Message::with('sender')->find($this->replyToId);
            if ($original) {
                $this->subject = str_starts_with(strtolower($original->subject), 're:')
                    ? $original->subject
                    : 'Re: ' . $original->subject;

                $senderName = $original->sender?->name ?? 'Unknown';
                $date = $original->created_at->format('Y-m-d H:i');
                $quoted = "> On {$date}, {$senderName} wrote:\n> " . str_replace("\n", "\n> ", $original->body);
                $this->body = "\n\n" . $quoted;
            }
        }

        $this->fixReceiver = (bool) $fixReceiver;
    }


    public function updatedSearch(string $value): void
    {
        if (strlen($value) >= 2) {
            $this->searchResults = User::query()
                // Search in 'name' (firstname + lastname) and 'username' in additional_infos table
                ->where(function ($query) use ($value) {
                    $query->where('firstname', 'like', '%' . $value . '%')
                        ->orWhere('lastname', 'like', '%' . $value . '%')
                        ->orWhereHas('additionalInfo', function ($subQuery) use ($value) {
                            $subQuery->where('username', 'like', '%' . $value . '%');
                        });
                })
                ->where('id', '!=', Auth::id()) // Exclude self
                ->whereDoesntHave('grant', function ($query) { // Exclude banned users
                    $query->where('is_banned', true);
                })
                ->select('id', 'firstname', 'lastname') // Select necessary fields
                ->with('additionalInfo:user_id,username') // Eager load username from additionalInfo
                ->take(10) // Limit results
                ->get()
                // Transform results to include a display name (username or full name)
                ->map(function (User $user) {
                    return [
                        'id' => $user->id,
                        // Prioritize username, fallback to firstname + lastname
                        'display_name' => $user->additionalInfo?->username ?: ($user->firstname . ' ' . $user->lastname),
                    ];
                })
                ->toArray();
            $this->showResults = true;
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
        $this->receiver_id = null; // Reset selected recipient when searching
        $this->selectedRecipientName = ''; // Clear selected name
    }

    public function selectRecipient(int $userId, string $displayName): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->receiver_id = $user->id;
            $this->selectedRecipientName = $displayName; // Use the display name from search results
            $this->search = ''; // Clear search input
            $this->searchResults = [];
            $this->showResults = false; // Hide results
        }
    }

    public function deselectRecipient(): void
    {
        if (!$this->fixReceiver) { // Only allow deselect if receiver is not fixed
            $this->receiver_id = null;
            $this->selectedRecipientName = '';
            $this->search = '';
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function sendMessage()
    {
        $this->validate();

        if (!$this->receiver_id) {
            $this->addError('receiver_id', 'Please select a recipient.');
            return;
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiver_id,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        return redirect()->route('mail.inbox')->with('message', 'Message sent!');
    }

    public function render()
    {
        return view('livewire.mail.message-compose');
    }
}