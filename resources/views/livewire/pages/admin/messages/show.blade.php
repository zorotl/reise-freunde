<?php

use App\Models\Message; 
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

// Apply the admin layout and middleware
new
#[Layout('components.layouts.admin.header')]
#[Middleware(['auth', 'admin_or_moderator'])]
#[Title('Admin - View Message')]
class extends Component {
    public ?Message $messageInstance = null; // Renamed to avoid conflict with Volt's $message
    public ?string $errorMessage = null;

    public function mount(int $messageId): void
    {
        if (!auth()->check() || !auth()->user()->isAdminOrModerator()) {
            abort(403, 'Unauthorized');
        }

        $this->messageInstance = Message::withTrashed()
            ->with([
                'sender' => fn ($query) => $query->withTrashed()->with(['additionalInfo', 'grant']),
                'receiver' => fn ($query) => $query->withTrashed()->with(['additionalInfo', 'grant']),
                // 'reports' // Uncomment if you have reports relationship and want to display them
            ])
            ->find($messageId);

        if (!$this->messageInstance) {
            $this->errorMessage = __('Message not found.');
        }
    }

    // Helper to format date or return N/A
    public function formatDate($date): string
    {
        return $date ? $date->format('Y-m-d H:i:s T') : __('N/A');
    }

    // Actions like soft delete, restore, force delete could be added here
    // For now, keeping it focused on display as actions are on the list view.

};
?>

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errorMessage)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">{{ __('Error!') }}</strong>
                    <span class="block sm:inline">{{ $errorMessage }}</span>
                </div>
                <a href="{{ route('admin.messages') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    &larr; {{ __('Back to Messages List') }}
                </a>
            @elseif ($messageInstance)
                <div class="mb-6">
                    <a href="{{ route('admin.messages') }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        &larr; {{ __('Back to Messages List') }}
                    </a>
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
                            {{-- Basic Info --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sender') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    @if ($messageInstance->sender)
                                        <a href="{{ route('admin.users', ['filterUserId' => $messageInstance->sender->id]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                            {{ $messageInstance->sender->additionalInfo->username ?? $messageInstance->sender->name }}
                                        </a>
                                        (ID: {{ $messageInstance->sender->id }})
                                        @if($messageInstance->sender->trashed()) <span class="text-xs text-red-500 dark:text-red-400 ml-1">({{__('User Deleted')}})</span> @endif
                                        @if($messageInstance->sender->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400 ml-1">({{__('User Banned')}})</span> @endif
                                    @else
                                        {{ __('User Not Found (Deleted)') }}
                                    @endif
                                </dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Receiver') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    @if ($messageInstance->receiver)
                                        <a href="{{ route('admin.users', ['filterUserId' => $messageInstance->receiver->id]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                            {{ $messageInstance->receiver->additionalInfo->username ?? $messageInstance->receiver->name }}
                                        </a>
                                        (ID: {{ $messageInstance->receiver->id }})
                                        @if($messageInstance->receiver->trashed()) <span class="text-xs text-red-500 dark:text-red-400 ml-1">({{__('User Deleted')}})</span> @endif
                                        @if($messageInstance->receiver->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400 ml-1">({{__('User Banned')}})</span> @endif
                                    @else
                                        {{ __('User Not Found (Deleted)') }}
                                    @endif
                                </dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sent At') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $this->formatDate($messageInstance->created_at) }}</dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Read At') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $this->formatDate($messageInstance->read_at) }}</dd>
                            </div>

                            {{-- Message Body --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Message Body') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    <div class="prose dark:prose-invert max-w-none whitespace-pre-line">{{ $messageInstance->body }}</div>
                                </dd>
                            </div>

                            {{-- Status Timestamps --}}
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50 dark:bg-neutral-700/50">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sender Status') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2 space-y-1">
                                    <div>{{ __('Archived:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_archived_at) }}</span></div>
                                    <div>{{ __('In Trash (Deleted):') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_deleted_at) }}</span></div>
                                    <div>{{ __('Permanently Deleted from Trash:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->sender_permanently_deleted_at) }}</span></div>
                                </dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Receiver Status') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2 space-y-1">
                                    <div>{{ __('Archived:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_archived_at) }}</span></div>
                                    <div>{{ __('In Trash (Deleted):') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_deleted_at) }}</span></div>
                                    <div>{{ __('Permanently Deleted from Trash:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->receiver_permanently_deleted_at) }}</span></div>
                                </dd>
                            </div>
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50 dark:bg-neutral-700/50">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Admin System Status') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    <div>{{ __('Soft Deleted by Admin:') }} <span class="font-semibold">{{ $this->formatDate($messageInstance->deleted_at) }}</span></div>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Admin Actions for this message could go here --}}
                {{-- For example, buttons to Soft Delete, Restore, Force Delete --}}
                {{-- Similar to what's in ManageMessages list, but for a single message --}}
                {{-- This part is optional for this specific task (displaying state) --}}
                {{--
                <div class="mt-6 flex justify-end space-x-3">
                    @if ($messageInstance->trashed())
                        <button wire:click="restoreMessage" class="btn-primary">{{ __('Restore Message (Admin)') }}</button>
                        <button wire:click="forceDeleteMessage" wire:confirm="PERMANENTLY DELETE this message for everyone?" class="btn-danger">{{ __('Force Delete Message (Admin)') }}</button>
                    @else
                        <button wire:click="adminSoftDeleteMessage" class="btn-warning">{{ __('Soft Delete Message (Admin)') }}</button>
                    @endif
                </div>
                --}}

            @else
                <div class="text-center py-12">
                    <flux:icon.backspace class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Message Not Found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('The requested message could not be found or has been deleted.') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.messages') }}" wire:navigate class="btn-primary">
                            {{ __('Go back to messages') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>