<?php

namespace App\Livewire\Mail;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Archived Messages')]
class ArchivedBox extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[On('messageUnarchived')]
    #[On('messageDeletedFromArchive')]
    #[On('userMessageActionFeedback')] // Listener for feedback
    public function handleFeedback($message = null, $type = 'status'): void
    {
        if ($message) {
            session()->flash($type, $message);
        }
        $this->resetPage();
    }

    public function getMessagesProperty()
    {
        if (!Auth::check()) {
            return Message::query()->whereRaw('1 = 0')->paginate(10);
        }
        $userId = Auth::id();
        return Message::query()
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->whereNotNull('receiver_archived_at')
                    ->whereNull('receiver_permanently_deleted_at') // From Part 1.1
                    ->whereNull('receiver_deleted_at');
            })->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->whereNotNull('sender_archived_at')
                    ->whereNull('sender_permanently_deleted_at')   // From Part 1.1
                    ->whereNull('sender_deleted_at');
            })
            ->select(['id', 'sender_id', 'receiver_id', 'subject', 'created_at', 'read_at', 'sender_archived_at', 'receiver_archived_at']) // SELECT
            ->with([
                'sender:id,firstname,lastname',
                'sender.additionalInfo:user_id,username',
                'receiver:id,firstname,lastname',
                'receiver.additionalInfo:user_id,username'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function unarchiveMessage(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message) {
            $unarchived = false;
            $currentUserId = Auth::id();
            if ($message->receiver_id === $currentUserId && $message->receiver_archived_at) {
                $message->receiver_archived_at = null;
                $unarchived = true;
            } elseif ($message->sender_id === $currentUserId && $message->sender_archived_at) {
                $message->sender_archived_at = null;
                $unarchived = true;
            }

            if ($unarchived) {
                $message->save();
                $this->dispatch('userMessageActionFeedback', message: __('Message unarchived.'), type: 'status');
                $this->dispatch('messageUnarchived');
            }
        }
    }

    public function deleteFromArchive(int $messageId): void
    {
        $message = Message::find($messageId);
        if ($message) {
            $deleted = false;
            $currentUserId = Auth::id();
            if ($message->receiver_id === $currentUserId && $message->receiver_archived_at) {
                $message->receiver_deleted_at = now();
                // Optionally also unarchive if you want it to disappear completely from archive view upon delete:
                // $message->receiver_archived_at = null;
                $deleted = true;
            } elseif ($message->sender_id === $currentUserId && $message->sender_archived_at) {
                $message->sender_deleted_at = now();
                // Optionally also unarchive:
                // $message->sender_archived_at = null;
                $deleted = true;
            }

            if ($deleted) {
                $message->save();
                $this->dispatch('userMessageActionFeedback', message: __('Message deleted from archive.'), type: 'status');
                $this->dispatch('messageDeletedFromArchive');
            }
        }
    }

    public function render()
    {
        return view('livewire.mail.archived-box', [
            'messages' => $this->messages,
        ]);
    }
}