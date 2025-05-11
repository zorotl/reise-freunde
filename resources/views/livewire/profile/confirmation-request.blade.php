<span>
    @if ($request)
        <p class="text-sm text-gray-600">{{ __('Bürgschaft-Request sent or already confirmed.') }}</p>
    @else
        <button wire:click="sendRequest"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Request Bürgschaft') }}
        </button>
    @endif

    @if (session('success'))
        <p class="mt-2 text-green-600">{{ session('success') }}</p>
    @endif
</span>
