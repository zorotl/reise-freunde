<?php

use Livewire\Volt\Component;
use App\Models\Message;
use App\Models\User;
use App\Notifications\AdminForceDeletedMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging errors
use Livewire\Attributes\{layout, middleware, title};

new 
#[Layout('components.layouts.admin.header')]
#[Middleware(['auth', 'admin_or_moderator'])]
#[Title('Admin - View Message')] 
class extends Component {
    public ?Message $messageInstance = null; // Renamed to avoid conflict if Volt uses $message
    public ?string $errorMessage = null;

    // Listener for feedback events, primarily if actions on this page refresh it
    #[On('adminMessageActionFeedback')]
    public function showFeedback($message, $type = 'message')
    {
        session()->flash($type, $message);
        // If an action like restore happens and we stay on the page, refresh the model
        if ($this->messageInstance && ($type === 'message' || $type === 'status')) { // 'status' often used for success
            $this->messageInstance->refresh();
        }
    }

    public function mount(int $messageId): void
    {
        if (!auth()->check() || !auth()->user()->isAdminOrModerator()) {
            session()->flash('error', 'Unauthorized access to admin area.');
            $this->redirectRoute('admin.dashboard', navigate: true); // Or a more general admin unauthorized page
            return;
        }

        $this->messageInstance = Message::withTrashed() // Admins see all, including system soft-deleted
            ->with([
                'sender' => fn ($query) => $query->withTrashed()->with(['additionalInfo', 'grant']),
                'receiver' => fn ($query) => $query->withTrashed()->with(['additionalInfo', 'grant']),
                // 'reports' // Uncomment if you have a reports relationship and want to display them
            ])
            ->find($messageId);

        if (!$this->messageInstance) {
            $this->errorMessage = __('Message not found.');
            // No redirect here, the blade will handle displaying the error message
        }
    }

    // Helper to format date or return N/A
    public function formatDate($date): string
    {
        return $date ? $date->format('Y-m-d H:i:s T') : __('N/A');
    }

    // --- ADMIN ACTION METHODS ---

    public function adminSoftDeleteMessage(): void
    {
        if (!$this->messageInstance || $this->messageInstance->trashed()) {
            $this->dispatch('adminMessageActionFeedback', message: __('Message already deleted or not found.'), type: 'error');
            return;
        }
        if (!auth()->user()?->isAdminOrModerator()) { // Added null-safe operator
            $this->dispatch('adminMessageActionFeedback', message: __('Unauthorized action.'), type: 'error');
            return;
        }

        try {
            $this->messageInstance->delete(); // SoftDeletes trait method
            $this->dispatch('messageDeleted', messageId: $this->messageInstance->id); // For list refresh
            session()->flash('message', __('Message soft-deleted by admin.'));
            $this->redirectRoute('admin.messages', navigate:true);
        } catch (\Exception $e) {
            Log::error('Admin soft delete message error: ' . $e->getMessage(), ['message_id' => $this->messageInstance?->id]);
            $this->dispatch('adminMessageActionFeedback', message: __('Failed to soft delete message.'), type: 'error');
        }
    }

    public function restoreMessage(): void
    {
        if (!$this->messageInstance || !$this->messageInstance->trashed()) {
            $this->dispatch('adminMessageActionFeedback', message: __('Message not deleted by admin or not found.'), type: 'error');
            return;
        }
        if (!auth()->user()?->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: __('Unauthorized action.'), type: 'error');
            return;
        }

        try {
            $this->messageInstance->restore();
            $this->messageInstance->refresh(); // Refresh model for current view
            $this->dispatch('messageRestored', messageId: $this->messageInstance->id); // For list refresh
            $this->dispatch('adminMessageActionFeedback', message: __('Message restored by admin.'), type: 'message'); // For this page's feedback
        } catch (\Exception $e) {
            Log::error('Admin restore message error: ' . $e->getMessage(), ['message_id' => $this->messageInstance?->id]);
            $this->dispatch('adminMessageActionFeedback', message: __('Failed to restore message.'), type: 'error');
        }
    }

    public function forceDeleteMessage(): void
    {
        if (!$this->messageInstance) {
            $this->dispatch('adminMessageActionFeedback', message: __('Message not found.'), type: 'error');
            return;
        }
        if (!auth()->user()?->isAdminOrModerator()) {
            $this->dispatch('adminMessageActionFeedback', message: __('Unauthorized action.'), type: 'error');
            return;
        }

        if ($this->messageInstance) {
            $messageToNotify = $this->messageInstance;
            $originalSubject = $messageToNotify->subject;
            $originalMessageId = $messageToNotify->id;
            $originalSenderId = $messageToNotify->sender_id; // Get sender ID
            $originalReceiverId = $messageToNotify->receiver_id; // Get receiver ID
            $senderUser = $messageToNotify->sender; // Get sender model
            $receiverUser = $messageToNotify->receiver; // Get receiver model
            $adminPerformingAction = Auth::user();

            try {
                $this->messageInstance->forceDelete();
                $this->dispatch('messageDeleted', messageId: $originalMessageId);
                session()->flash('message', __('Message permanently deleted by admin.'));

                if ($adminPerformingAction) {
                    $adminName = $adminPerformingAction->firstname . ' ' . $adminPerformingAction->lastname;

                    if ($senderUser && $senderUser->id !== $adminPerformingAction->id && $senderUser instanceof User) {
                        $senderUser->notify(new AdminForceDeletedMessageNotification($originalSubject, $originalMessageId, $adminName, $originalSenderId, $originalReceiverId));
                    }
                    if ($receiverUser && $receiverUser->id !== $adminPerformingAction->id && $receiverUser instanceof User) {
                        $receiverUser->notify(new AdminForceDeletedMessageNotification($originalSubject, $originalMessageId, $adminName, $originalSenderId, $originalReceiverId));
                    }
                }
                $this->redirectRoute('admin.messages', navigate:true);
            } catch (\Exception $e) {
                Log::error('Admin force delete message error from detail view: ' . $e->getMessage(), ['message_id' => $originalMessageId]);
                // Re-fetch message if delete failed, so the view doesn't break if redirect doesn't happen
                $this->messageInstance = Message::withTrashed()->find($originalMessageId);
                $this->dispatch('adminMessageActionFeedback', message: __('Failed to permanently delete message.'), type: 'error');
            }
        }
    }
}; ?>

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Feedback Messages --}}
            @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <flux:icon.check-circle class="h-5 w-5 text-green-400 dark:text-green-300" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif
            @if (session()->has('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <flux:icon.x-circle class="h-5 w-5 text-red-400 dark:text-red-300" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if ($errorMessage)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">{{ __('Error!') }}</strong>
                    <span class="block sm:inline">{{ $errorMessage }}</span>
                </div>
                <a href="{{ route('admin.messages') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    &larr; {{ __('Back to Messages List') }}
                </a>
            @elseif ($messageInstance)
                <div class="mb-6 flex justify-between items-center flex-wrap gap-2">
                    <a href="{{ route('admin.messages') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        &larr; {{ __('Back to Messages List') }}
                    </a>
                    {{-- ACTION BUTTONS --}}
                    <div class="flex space-x-2">
                        @if ($messageInstance->trashed()) {{-- Message is Admin Soft-Deleted --}}
                            <button wire:click="restoreMessage"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-500 dark:hover:bg-green-400">
                                <flux:icon.arrow-path class="w-4 h-4 mr-1.5"/>
                                {{ __('Restore') }}
                            </button>
                            <button wire:click="forceDeleteMessage"
                                    wire:confirm="Are you sure you want to PERMANENTLY delete this message from the system? This action cannot be undone."
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-400">
                                <flux:icon.trash class="w-4 h-4 mr-1.5"/>
                                {{ __('Force Delete') }}
                            </button>
                        @else
                            <button wire:click="adminSoftDeleteMessage"
                                    wire:confirm="Are you sure you want to soft-delete this message (admin action)? This will hide it but can be restored."
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 dark:bg-yellow-600 dark:hover:bg-yellow-500">
                                <flux:icon.archive-box-arrow-down class="w-4 h-4 mr-1.5"/>
                                {{ __('Soft Delete') }}
                            </button>
                             <button wire:click="forceDeleteMessage"
                                    wire:confirm="Are you sure you want to PERMANENTLY delete this message from the system? This action cannot be undone."
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-400">
                                <flux:icon.trash class="w-4 h-4 mr-1.5"/>
                                {{ __('Force Delete') }}
                            </button>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Message Details') }} (ID: {{ $messageInstance->id }})
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Subject:') }} {{ $messageInstance->subject }}
                        </p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-neutral-700 px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-200 dark:sm:divide-neutral-700">
                            {{-- Sender Info --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sender') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    @if ($messageInstance->sender)
                                        <a href="{{ route('admin.users', ['filterUserId' => $messageInstance->sender->id]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                            {{ $messageInstance->sender->additionalInfo->username ?? $messageInstance->sender->name }}
                                        </a>
                                        (ID: {{ $messageInstance->sender->id }})
                                        @if($messageInstance->sender->trashed()) <span class="text-xs text-red-500 dark:text-red-400 ml-1">({{__('User Account Deleted')}})</span> @endif
                                        @if($messageInstance->sender->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400 ml-1">({{__('User Banned')}})</span> @endif
                                    @else
                                        <span class="italic">{{ __('User Not Found (System or Hard Deleted)') }}</span>
                                    @endif
                                </dd>
                            </div>
                            {{-- Receiver Info --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Receiver') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                     @if ($messageInstance->receiver)
                                        <a href="{{ route('admin.users', ['filterUserId' => $messageInstance->receiver->id]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                            {{ $messageInstance->receiver->additionalInfo->username ?? $messageInstance->receiver->name }}
                                        </a>
                                        (ID: {{ $messageInstance->receiver->id }})
                                        @if($messageInstance->receiver->trashed()) <span class="text-xs text-red-500 dark:text-red-400 ml-1">({{__('User Account Deleted')}})</span> @endif
                                        @if($messageInstance->receiver->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400 ml-1">({{__('User Banned')}})</span> @endif
                                    @else
                                        <span class="italic">{{ __('User Not Found (System or Hard Deleted)') }}</span>
                                    @endif
                                </dd>
                            </div>
                            {{-- Sent At & Read At --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sent At') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $this->formatDate($messageInstance->created_at) }}</dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Read At by Receiver') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $this->formatDate($messageInstance->read_at) }}</dd>
                            </div>

                            {{-- Message Body --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Message Body') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    <div class="prose dark:prose-invert max-w-none whitespace-pre-line p-3 bg-gray-50 dark:bg-neutral-700/30 rounded-md border dark:border-neutral-600">{{ $messageInstance->body }}</div>
                                </dd>
                            </div>

                            {{-- Status Timestamps Section --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-1 sm:gap-4 sm:px-6 bg-gray-50 dark:bg-neutral-900/30">
                                <dt class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Message Status Flags') }}</dt>
                                <dd class="sm:col-span-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('Sender Actions:') }}</h4>
                                            <ul class="list-disc list-inside text-sm text-gray-800 dark:text-gray-200 ml-4">
                                                <li>{{ __('Archived:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_archived_at) }}</span></li>
                                                <li>{{ __('In Trash (Deleted):') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_deleted_at) }}</span></li>
                                                <li>{{ __('Perm. Deleted from Trash:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_permanently_deleted_at) }}</span></li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('Receiver Actions:') }}</h4>
                                            <ul class="list-disc list-inside text-sm text-gray-800 dark:text-gray-200 ml-4">
                                                <li>{{ __('Archived:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_archived_at) }}</span></li>
                                                <li>{{ __('In Trash (Deleted):') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_deleted_at) }}</span></li>
                                                <li>{{ __('Perm. Deleted from Trash:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_permanently_deleted_at) }}</span></li>
                                            </ul>
                                        </div>
                                        <div class="md:col-span-2 mt-2">
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('Admin System Status:') }}</h4>
                                            <ul class="list-disc list-inside text-sm text-gray-800 dark:text-gray-200 ml-4">
                                                <li>{{ __('Soft Deleted by Admin:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->deleted_at) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <flux:icon.backspace class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Message Not Found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $errorMessage ?: __('The requested message could not be found or has been deleted from the system.') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.messages') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            {{ __('Go back to messages') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
