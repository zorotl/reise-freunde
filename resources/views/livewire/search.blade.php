<div>
    <div class="mb-4">
        <label for="search" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ __('Search for
            Adventures') }}</label>
        <input wire:model.live="query" type="text" id="search"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            placeholder="{{ __('Search by title and content, otherwise use filters below...') }}">

        @if (strlen($query) >= 2 && !empty($results))
        <div
            class="absolute z-10 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-md shadow-lg mt-1 w-full">
            <ul>
                @forelse ($results as $result)
                <li class="p-2 hover:bg-gray-100 dark:hover:bg-neutral-700">
                    <a href="{{ route('post.single', ['post' => $result->id]) }}">{{ $result->title }}</a>
                </li>
                @empty
                <li class="p-2 text-gray-500 dark:text-gray-400">{{ __('No results found.') }}</li>
                @endforelse
            </ul>
        </div>
        @endif
    </div>
</div>