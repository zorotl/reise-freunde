<div class="py-8">
    {{-- Navigation --}}
    <div class="mb-6 flex items-center justify-between">
        <a wire:navigate href="{{ $fromWhere === 'outbox' ? route('mail.outbox') : route('mail.inbox') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
            {{ $fromWhere === 'outbox' ? __('Back to Outbox') : __('Back to Inbox') }}
        </a>

        @unless ($fromWhere == 'outbox')
        <a wire:navigate
            href="{{ route('mail.compose', ['receiverId' => $message->sender->id, 'fixReceiver' => true]) }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
            {{ __('Reply') }}
        </a>
        @endunless
    </div>

    {{-- Message Card --}}
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ $message->subject }}
            </h2>

            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <div>
                    <strong>{{ __('From:') }}</strong>
                    {{ $message->sender->additionalInfo->username }}
                </div>
                <div>
                    <strong>{{ __('To:') }}</strong>
                    {{ $message->receiver->additionalInfo->username }}
                </div>
                <div>
                    <strong>{{ __('Date:') }}</strong>
                    {{ $message->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>

        <div class="px-4 py-6 sm:px-6">
            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">
                {{ $message->body }}
            </p>
        </div>

        <div
            class="px-4 py-3 bg-gray-50 dark:bg-neutral-900 text-right sm:px-6 border-t border-gray-200 dark:border-neutral-700">
            <button type="button" onclick="history.back()"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</div>