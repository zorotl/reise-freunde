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

    {{-- Controls: Search and Per Page --}}
    <div class="mb-4 flex justify-between items-center">
        <div class="flex-1 me-4">
            <input wire:model.live="search" type="text" placeholder="{{ __('Search messages...') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div>
            <label for="perPage" class="sr-only">{{ __('Per Page') }}</label>
            <select wire:model.live="perPage" id="perPage"
                class="shadow border rounded py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Message Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    {{-- Subject Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('subject')">
                        {{ __('Subject') }}
                        @if ($sortField === 'subject')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Sender Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('sender_id')">
                        {{ __('Sender') }}
                        @if ($sortField === 'sender_id')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Receiver Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('receiver_id')">
                        {{ __('Receiver') }}
                        @if ($sortField === 'receiver_id')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Sent At Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('created_at')">
                        {{ __('Sent At') }}
                        @if ($sortField === 'created_at')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Status Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                    {{-- Actions Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Actions') }} </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($messages as $message)
                {{-- Highlight if sender is soft deleted --}}
                <tr @if($message->sender && $message->sender->trashed()) class="bg-yellow-100 dark:bg-yellow-900/50
                    opacity-75" @endif>
                    {{-- Subject Cell - added max-w-xs and truncate/overflow classes --}}
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs overflow-hidden text-ellipsis">
                        {{ $message->subject }}
                    </td>
                    {{-- Sender Cell - added max-w-xs and truncate/overflow classes --}}
                    <td
                        class="px-6 py-4 whitespace-normal  text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        @if($message->sender)
                        <a href="{{ route('user.profile', $message->sender->id) }}"
                            class="text-blue-600 hover:underline">
                            {{ $message->sender->name }} ({{ $message->sender->email }})
                        </a>
                        @if($message->sender->trashed())
                        <span class="ms-1 text-red-500">({{ __('Banned') }})</span>
                        @endif
                        @else
                        {{ __('Deleted User') }}
                        @endif
                    </td>
                    {{-- Receiver Cell - added max-w-xs and truncate/overflow classes --}}
                    <td
                        class="px-6 py-4 whitespace-normal  text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        @if($message->receiver)
                        <a href="{{ route('user.profile', $message->receiver->id) }}"
                            class="text-blue-600 hover:underline">
                            {{ $message->receiver->name }} ({{ $message->receiver->email }})
                        </a>
                        @if($message->receiver->trashed())
                        <span class="ms-1 text-red-500">({{ __('Banned') }})</span>
                        @endif
                        @else
                        {{ __('Deleted User') }}
                        @endif
                    </td>
                    {{-- Sent At Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </td>
                    {{-- Status Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if($message->read_at)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">{{
                            __('Read') }}</span>
                        <br><span class="text-xs">{{ $message->read_at->format('Y-m-d H:i') }}</span>
                        @else
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">{{
                            __('Unread') }}</span>
                        @endif
                        @if($message->trashed())
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">{{
                            __('Msg Deleted') }}</span>
                        @endif
                    </td>
                    {{-- Actions Cell - increased width again --}}
                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium w-56"> {{-- <--- Changed from
                            w-48 to w-56 --}} {{-- Action Button: Ban Sender --}} @if($message->sender &&
                            !$message->sender->trashed())
                            <button wire:click="banSender({{ $message->sender->id }})"
                                onclick="return confirm('{{ __('Are you sure you want to ban this sender? This will soft delete the user.') }}')"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">{{
                                __('Ban Sender') }}</button>
                            @elseif($message->sender && $message->sender->trashed())
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Sender Banned') }}</span>
                            @else
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Sender Deleted') }}</span>
                            @endif

                            {{-- Optional: Add View Message, Soft Delete Message, etc. buttons here later --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ __('No messages found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</div>