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
        {{-- Back Button --}}
        <a wire:navigate href="{{ match($fromWhere) {
            'outbox' => route('mail.outbox'),
            'archive' => route('mail.archive'),
            default => route('mail.inbox'),
        } }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
            <flux:icon.arrow-left class="w-4 h-4 mr-2" />
            {{ match($fromWhere) {
                'outbox' => __('Back to Outbox'),
                'archive' => __('Back to Archive'),
                default => __('Back to Inbox'),
            } }}
        </a>

        <div class="flex items-center gap-3">
            {{-- Reply (visible only when received message) --}}
            @if (in_array($fromWhere, ['inbox', 'archive']) && $message->receiver_id === auth()->id())
                <a wire:navigate
                    href="{{ route('mail.compose', [
                        'receiverId' => $message->sender->id,
                        'fixReceiver' => true,
                        'replyToId' => $message->id
                        // 'subject' => Str::startsWith($message->subject, 'Re: ') ? $message->subject : 'Re: ' . $message->subject
                    ]) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-md hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                    <flux:icon.arrow-uturn-left class="w-4 h-4 mr-2" />
                    {{ __('Reply') }}
                </a>
            @endif

            {{-- Burger Menu --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-black dark:text-gray-300 dark:hover:text-white transition focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-offset-neutral-800">
                    <flux:icon.bars-3 />
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-52 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-md shadow-lg z-10 py-2">

                    {{-- Archive --}}
                    @if ($fromWhere !== 'archive' && !$message->isArchivedByCurrentUser() && !$message->isDeletedByCurrentUser())
                        <button wire:click="archiveMessage"
                            wire:confirm="{{ __('Are you sure you want to archive this message?') }}"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <flux:icon.archive-box class="inline-block w-4 h-4 mr-2" />
                            {{ __('Archive') }}
                        </button>
                    @endif

                    {{-- Unarchive --}}
                    @if ($fromWhere === 'archive' && !$message->isDeletedByCurrentUser())
                        <button wire:click="unarchiveMessage"
                            wire:confirm="{{ __('Are you sure you want to unarchive this message?') }}"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <flux:icon.arrow-uturn-up class="inline-block w-4 h-4 mr-2" />
                            {{ __('Unarchive') }}
                        </button>
                    @endif

                    {{-- Delete --}}
                    @if (!$message->isDeletedByCurrentUser())
                        <button wire:click="deleteMessage"
                            wire:confirm="{{ __('Are you sure you want to delete this message' . ($fromWhere === 'archive' ? ' from archive' : '') . '?') }}"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <flux:icon.trash class="inline-block w-4 h-4 mr-2" />
                            {{ __('Delete') }}
                        </button>
                    @endif

                    {{-- Report --}}
                    @if ($message->receiver_id === auth()->id() && !$message->isDeletedByCurrentUser() && !$message->isArchivedByCurrentUser())
                        <button
                            wire:click="$dispatch('openReportMessageModal', {
                                messageId: {{ $message->id }},
                                snippet: '{{ Str::limit(addslashes($message->body), 50) }}'
                            })"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <flux:icon.flag class="inline-block w-4 h-4 mr-2" />
                            {{ __('Report') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Message Card --}}
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        {{-- Header / Meta --}}
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $message->subject }}
            </h2>
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <dt class="font-semibold">{{ __('From') }}</dt>
                    <dd class="mt-0.5 text-gray-800 dark:text-gray-200">
                        {{ $message->sender->additionalInfo->username ?? $message->sender->name }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold">{{ __('To') }}</dt>
                    <dd class="mt-0.5 text-gray-800 dark:text-gray-200">
                        {{ $message->receiver->additionalInfo->username ?? $message->receiver->name }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold">{{ __('Date') }}</dt>
                    <dd class="mt-0.5 text-gray-800 dark:text-gray-200">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Body --}}
        <div class="px-4 py-6 sm:px-6">
            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="whitespace-pre-line">
                    {{ $message->body }}
                </p>
            </div>
        </div>
    </div>

    <livewire:report-message-modal />
</div>
