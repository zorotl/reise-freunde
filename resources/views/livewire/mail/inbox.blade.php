<div>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Inbox
            @if ($unreadCount > 0)
            <span
                class="inline-flex items-center ml-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-700 text-white">{{
                $unreadCount }}</span>
            @endif
        </h2>
        <div>
            <a wire:navigate href="{{ route('mail.outbox') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Outbox
            </a>
            <a wire:navigate href="{{ route('mail.compose') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                Compose
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
            @if ($messages->count() > 0)
            @foreach ($messages as $message)
            <li wire:key="{{ $message->id }}">
                <a href="{{ route('mail.messages.view', [
                            $message, 
                            'fromWhere' => 'inbox'
                            ])}}" class="block hover:bg-gray-50 dark:hover:bg-neutral-700">
                    <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                        <div class="truncate">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{
                                    $message->sender->name }}</h3>
                                @if (!$message->read_at)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">New</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">{{ $message->subject }}
                            </p>
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->diffForHumans()
                                }}</p>
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
            @else
            <li class="px-4 py-4 sm:px-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Your inbox is empty.</p>
            </li>
            @endif
        </ul>
    </div>
</div>