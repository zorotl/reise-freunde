<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Trash') }}
    </h1>

    <x-mail-tab-switcher />

    @if (session()->has('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-200 border border-green-300 dark:border-green-600">
            {{ session('status') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600">
            {{ session('error') }}
        </div>
    @endif

    @if ($this->messages->count() > 0)
    <div
        class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden divide-y divide-gray-200 dark:divide-neutral-700">
        @foreach ($this->messages as $message)
        <div wire:key="trash-message-{{ $message->id }}"
            class="hover:bg-gray-50 dark:hover:bg-neutral-700 px-4 py-4 sm:px-6 flex items-center justify-between gap-2">
            {{-- Message Info (Non-clickable or different link if needed) --}}
            <div class="flex-grow truncate">
                <div class="flex items-center space-x-3">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        @if ($message->sender_id === auth()->id())
                            {{-- Message was sent by current user --}}
                            {{ __('To:') }} {{ $message->receiver->additionalInfo->username ?? $message->receiver->name }}
                        @else
                            {{-- Message was received by current user --}}
                            {{ __('From:') }} {{ $message->sender->additionalInfo->username ?? $message->sender->name }}
                        @endif
                    </h3>
                    @if (!$message->read_at && $message->receiver_id === auth()->id())
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            {{ __('Unread') }}
                        </span>
                    @elseif ($message->read_at && $message->sender_id === auth()->id())
                         <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-neutral-600 text-gray-700 dark:text-gray-200">
                            {{ __('Read by recipient') }}
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">
                    {{ $message->subject }}
                </p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate italic">
                    @if($message->sender_id === auth()->id() && $message->sender_deleted_at)
                        {{ __('Deleted:') }} {{ $message->sender_deleted_at->format('Y-m-d H:i') }}
                    @elseif($message->receiver_id === auth()->id() && $message->receiver_deleted_at)
                        {{ __('Deleted:') }} {{ $message->receiver_deleted_at->format('Y-m-d H:i') }}
                    @endif
                </p>
            </div>
            <div class="ml-2 flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                {{ $message->created_at->diffForHumans() }}
            </div>
            {{-- Action Buttons --}}
            <div class="flex space-x-1 flex-shrink-0">
                <button wire:click="restoreMessage({{ $message->id }})"
                    class="p-1.5 text-xs text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 rounded-md hover:bg-blue-100 dark:hover:bg-blue-700" title="{{ __('Restore Message') }}">
                    <flux:icon.arrow-uturn-left class="w-4 h-4"/>
                </button>
                 <button wire:click="deletePermanently({{ $message->id }})" wire:confirm="Are you sure you want to permanently delete this message from your view? This action cannot be undone by you."
                    class="p-1.5 text-xs text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-300 rounded-md hover:bg-red-100 dark:hover:bg-red-700" title="{{ __('Delete Permanently') }}">
                    <flux:icon.trash class="w-4 h-4"/>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $this->messages->links() }}
    </div>
    @else
    <div class="text-center text-gray-500 dark:text-gray-400 py-10">
        {{ __('Your trash is empty.') }}
    </div>
    @endif
</div>