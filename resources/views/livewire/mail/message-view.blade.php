<div class="py-8">
    {{-- Flash messages --}}
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

    {{-- Navigation and Actions --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <a wire:navigate href="{{ match($fromWhere) { 'outbox' => route('mail.outbox'), 'archive' => route('mail.archive'), default => route('mail.inbox') } }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
            {{ match($fromWhere) { 'outbox' => __('Back to Outbox'), 'archive' => __('Back to Archive'), default => __('Back to Inbox') } }}
        </a>

        <div class="flex items-center gap-3">
            {{-- Archive/Unarchive Button --}}
            @if ($fromWhere === 'archive')
                @if (!$message->isDeletedByCurrentUser())
                    <button wire:click="unarchiveMessage" wire:confirm="Are you sure you want to unarchive this message?"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-400 dark:hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                        <flux:icon.arrow-uturn-up class="w-4 h-4 mr-2"/> {{-- Using a different icon for unarchive --}}
                        {{ __('Unarchive') }}
                    </button>
                @endif
            @elseif (!$message->isArchivedByCurrentUser() && !$message->isDeletedByCurrentUser())
                 <button wire:click="archiveMessage" wire:confirm="Are you sure you want to archive this message?"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 dark:bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-yellow-400 dark:hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-150">
                    <flux:icon.archive-box class="w-4 h-4 mr-2"/>
                    {{ __('Archive') }}
                </button>
            @endif

            {{-- Delete Button (Contextual) --}}
            @if (!$message->isDeletedByCurrentUser())
                <button wire:click="deleteMessage" wire:confirm="Are you sure you want to delete this message{{ $fromWhere === 'archive' ? ' from archive' : '' }}?"
                    class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-red-500 dark:hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150">
                    <flux:icon.trash class="w-4 h-4 mr-2"/>
                    {{ __('Move to Trash') }}
                </button>
            @endif

            {{-- Reply and Report only from inbox/archive view of received message --}}
            @if (in_array($fromWhere, ['inbox', 'archive']) && $message->receiver_id === auth()->id())
                <a wire:navigate
                    href="{{ route('mail.compose', ['receiverId' => $message->sender->id, 'fixReceiver' => true, 'subject' => Str::startsWith($message->subject, 'Re: ') ? $message->subject : 'Re: ' . $message->subject]) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                    <flux:icon.arrow-uturn-left class="w-4 h-4 mr-2"/>
                    {{ __('Reply') }}
                </a>
                @if(!$message->isDeletedByCurrentUser() && !$message->isArchivedByCurrentUser())
                <button
                    wire:click="$dispatch('openReportMessageModal', { messageId: {{ $message->id }}, snippet: '{{ Str::limit(addslashes($message->body), 50) }}' })"
                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 border border-red-300 dark:border-red-700 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-red-200 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150">
                    <flux:icon.flag class="w-4 h-4 mr-2"/>
                    {{ __('Report') }}
                </button>
                @endif
            @endif
        </div>
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
                    {{ $message->sender->additionalInfo->username ?? $message->sender->name }}
                </div>
                <div>
                    <strong>{{ __('To:') }}</strong>
                    {{ $message->receiver->additionalInfo->username ?? $message->receiver->name }}
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
    </div>
    <livewire:report-message-modal />
</div>