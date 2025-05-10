<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Inbox') }}
        @if ($unreadCount > 0)
        <span
            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-700 text-white">
            {{ $unreadCount }}
        </span>
        @endif
    </h1>

    {{-- Tab Switcher --}}
    <x-mail-tab-switcher />

    {{-- Message List --}}
    @if ($messages->count() > 0)
    <div
        class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden divide-y divide-gray-200 dark:divide-neutral-700">
        @foreach ($messages as $message)
        <a wire:navigate href="{{ route('mail.messages.view', [$message, 'fromWhere' => 'inbox']) }}"
            class="block hover:bg-gray-50 dark:hover:bg-neutral-700 px-4 py-4 sm:px-6 flex items-center justify-between">
            <div class="truncate">
                <div class="flex items-center space-x-3">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $message->sender->additionalInfo->username }}
                    </h3>
                    @if (!$message->read_at)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        {{ __('New') }}
                    </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">
                    {{ $message->subject }}
                </p>
            </div>
            <div class="ml-2 flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                {{ $message->created_at->diffForHumans() }}
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>
    @else
    <div class="text-center text-gray-500 dark:text-gray-400 py-10">
        {{ __('Your inbox is empty.') }}
    </div>
    @endif
</div>