<div>
    <div class="mb-4 flex items-center justify-end">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Outbox</h2>
        <div class="ml-auto">
            <a wire:navigate href="{{ route('mail.inbox') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Inbox
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
                            'fromWhere' => 'outbox'
                            ]) }}" class="block hover:bg-gray-50 dark:hover:bg-neutral-700">
                    <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                        <div class="truncate">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">To: {{
                                $message->receiver->additionalInfo->username }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">{{ $message->subject }}
                            </p>
                        </div>
                        <div class="ml-2 flex-shrink-0 flex flex-col items-end">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->diffForHumans()
                                }}</p>
                            @if ($message->read_at)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 mt-1">Read</span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 mt-1">Unread</span>
                            @endif
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
            @else
            <li class="px-4 py-4 sm:px-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Your outbox is empty.</p>
            </li>
            @endif
        </ul>
    </div>
</div>