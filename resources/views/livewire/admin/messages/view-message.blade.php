<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                <title>Close</title>
                <path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L6.305 5.107a1.2 1.2 0 0 1 1.697-1.697l2.757 3.15 2.651-3.029a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.697z" />
            </svg>
        </span>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                <title>Close</title>
                <path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L6.305 5.107a1.2 1.2 0 0 1 1.697-1.697l2.757 3.15 2.651-3.029a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.697z" />
            </svg>
        </span>
    </div>
    @endif

    {{-- Back Button and Ban Sender Button --}}
    <div class="mb-4 flex items-center justify-between">
        <a wire:navigate href="{{ route('admin.messages') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Back to Message List') }}
        </a>

        {{-- Ban Sender Button (only visible to Admin/Moderator) --}}
        @if ($this->isAdminOrModerator && $this->message->sender && !$this->message->sender->trashed() &&
        !$this->message->sender->isAdminOrModerator())
        <button wire:click="banSender({{ $this->message->sender->id }})"
            onclick="return confirm('{{ __('Are you sure you want to ban this sender? This will set them as banned in user grants.') }}')"
            class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-red-500 dark:hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('Ban Sender') }}
        </button>
        @endif
    </div>

    {{-- Message Details Card --}}
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->message->subject }}</h2>
            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                {{-- Link Sender to Admin User Management --}}
                <span>{{ __('From:') }}
                    @if($this->message->sender)
                    <a href="{{ route('admin.users', ['filterUserId' => $this->message->sender->id]) }}"
                        class="text-blue-600 hover:underline" wire:navigate>
                        {{ $this->message->sender->name }}
                        @if($this->message->sender->trashed() || ($this->message->sender->grant &&
                        $this->message->sender->grant->is_banned))
                        <span class="ms-1 text-red-500">({{ __('Banned') }})</span>
                        @endif
                    </a>
                    @else
                    {{ __('Deleted User') }}
                    @endif
                </span>
                <span class="mx-2">&middot;</span>
                {{-- Link Receiver to Admin User Management --}}
                <span>{{ __('To:') }}
                    @if($this->message->receiver)
                    <a href="{{ route('admin.users', ['filterUserId' => $this->message->receiver->id]) }}"
                        class="text-blue-600 hover:underline" wire:navigate>
                        {{ $this->message->receiver->name }}
                        @if($this->message->receiver->trashed() || ($this->message->receiver->grant &&
                        $this->message->receiver->grant->is_banned))
                        <span class="ms-1 text-red-500">({{ __('Banned') }})</span>
                        @endif
                    </a>
                    @else
                    {{ __('Deleted User') }}
                    @endif
                </span>
                <span class="mx-2">&middot;</span>
                <span>{{ __('Date:') }} {{ $this->message->created_at->format('Y-m-d H:i') }}</span>
                @if($this->message->read_at)
                <span class="mx-2">&middot;</span>
                <span>{{ __('Read At:') }} {{ $this->message->read_at->format('Y-m-d H:i') }}</span>
                @endif
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-neutral-700 px-4 py-5 sm:p-6">
            <p class="text-gray-900 dark:text-gray-100">{{ $this->message->body }}</p>
        </div>
        {{-- No close button needed here, user can use the Back button --}}
        {{-- <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-900 text-right sm:px-6"> ... </div> --}}
    </div>
</div>