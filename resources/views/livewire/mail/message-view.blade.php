<div>
    <div class="mb-4 flex items-center justify-between">
        <a wire:navigate href="{{ route('mail.inbox') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            Back to Inbox
        </a>
        @unless ($fromWhere == 'outbox')
        <a wire:navigate href="{{ route('mail.compose', [
            'receiverId' => $message->sender->id, 
            'fixReceiver' => true
            ]) }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Reply
        </a>
        @endunless
    </div>

    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $message->subject }}</h2>
            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span>From: {{ $message->sender->additionalInfo->username }}</span>
                <span class="mx-2">&middot;</span>
                <span>To: {{ $message->receiver->additionalInfo->username }}</span>
                <span class="mx-2">&middot;</span>
                <span>Date: {{ $message->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-neutral-700 px-4 py-5 sm:p-6">
            <p class="text-gray-900 dark:text-gray-100">{{ $message->body }}</p>
        </div>
        <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-900 text-right sm:px-6">
            <button type="button" onclick="history.back()"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Close
            </button>
        </div>
    </div>
</div>