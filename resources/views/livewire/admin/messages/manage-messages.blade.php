<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('message'))
    <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/50">
        <div class="flex">
            <div class="flex-shrink-0">
                <flux:icon.check-circle class="h-5 w-5 text-green-400 dark:text-green-300" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
            </div>
        </div>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/50">
        <div class="flex">
            <div class="flex-shrink-0">
                <flux:icon.x-circle class="h-5 w-5 text-red-400 dark:text-red-300" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Controls: Search and Per Page --}}
    <div class="mb-4 flex flex-wrap justify-between items-center gap-4">
        <div class="flex-grow">
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="{{ __('Search messages...') }}"
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
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('subject')">
                        {{ __('Subject') }} @if ($sortField === 'subject') <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('sender_id')">
                        {{ __('Sender') }} @if ($sortField === 'sender_id') <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('receiver_id')">
                        {{ __('Receiver') }} @if ($sortField === 'receiver_id') <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('created_at')">
                        {{ __('Sent At') }} @if ($sortField === 'created_at') <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($messages as $message)
                <tr wire:key="admin-message-{{ $message->id }}"
                    @class([
                        'bg-red-100 dark:bg-red-900/30 opacity-60' => $message->trashed(), // Admin soft-deleted
                        'bg-yellow-50 dark:bg-yellow-900/20 opacity-80' => !$message->trashed() && ($message->sender_deleted_at || $message->receiver_deleted_at), // User soft-deleted
                        'bg-blue-50 dark:bg-blue-900/20 opacity-90' => !$message->trashed() && ($message->sender_archived_at || $message->receiver_archived_at) && !($message->sender_deleted_at || $message->receiver_deleted_at), // User archived
                    ])
                >
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs overflow-hidden text-ellipsis">
                        <a href="{{ route('admin.messages.show', $message->id) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                            {{ Str::limit($message->subject, 40) }}
                        </a>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        @if($message->sender)
                            <a href="{{ route('admin.users', ['filterUserId' => $message->sender->id]) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                {{ $message->sender->additionalInfo->username ?? $message->sender->name }}
                            </a>
                            @if($message->sender->trashed()) <span class="text-xs text-red-500 dark:text-red-400">(User Deleted)</span>
                            @elseif($message->sender->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400">(User Banned)</span>
                            @elseif(!$message->sender->isAdminOrModerator())
                            <button wire:click="banSender({{ $message->sender->id }})"
                                class="ml-1 px-1 py-0.5 text-xs font-semibold text-red-500 border border-red-500 rounded hover:bg-red-100 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-700/50"
                                title="Ban {{ $message->sender->name }}">
                                Ban
                            </button>
                            @endif
                        @else
                            <span class="italic text-gray-400 dark:text-gray-500">{{ __('Deleted User') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        @if($message->receiver)
                            <a href="{{ route('admin.users', ['filterUserId' => $message->receiver->id]) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                {{ $message->receiver->additionalInfo->username ?? $message->receiver->name }}
                            </a>
                            @if($message->receiver->trashed()) <span class="text-xs text-red-500 dark:text-red-400">(User Deleted)</span>
                            @elseif($message->receiver->grant?->is_banned) <span class="text-xs text-orange-500 dark:text-orange-400">(User Banned)</span>
                            @endif
                        @else
                            <span class="italic text-gray-400 dark:text-gray-500">{{ __('Deleted User') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if($message->trashed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-100">{{ __('Admin Deleted') }}</span>
                        @else
                            @if($message->read_at)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200">{{ __('Read') }}</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200">{{ __('Unread') }}</span>
                            @endif
                            @if($message->sender_deleted_at) 
                                <span class="block mt-1 px-2 text-xs rounded-full bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300" title="Deleted by sender: {{ $message->sender_deleted_at->format('Y-m-d H:i') }}">S:Del</span> 
                            @endif
                            @if($message->receiver_deleted_at) 
                                <span class="block mt-1 px-2 text-xs rounded-full bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300" title="Deleted by receiver: {{ $message->receiver_deleted_at->format('Y-m-d H:i') }}">R:Del</span> 
                            @endif
                            @if($message->sender_archived_at) 
                                <span class="block mt-1 px-2 text-xs rounded-full bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-300" title="Archived by sender: {{ $message->sender_archived_at->format('Y-m-d H:i') }}">S:Arch</span> 
                            @endif
                            @if($message->receiver_archived_at) 
                                <span class="block mt-1 px-2 text-xs rounded-full bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-300" title="Archived by receiver: {{ $message->receiver_archived_at->format('Y-m-d H:i') }}">R:Arch</span> 
                            @endif
                            @if($message->sender_permanently_deleted_at)
                                <span class="block mt-1 px-2 text-xs rounded-full bg-red-200 text-red-700 dark:bg-red-700 dark:text-red-200" title="Sender perm deleted: {{ $message->sender_permanently_deleted_at->format('Y-m-d H:i') }}">S:PDel</span>
                            @endif
                            @if($message->receiver_permanently_deleted_at)
                                <span class="block mt-1 px-2 text-xs rounded-full bg-red-200 text-red-700 dark:bg-red-700 dark:text-red-200" title="Receiver perm deleted: {{ $message->receiver_permanently_deleted_at->format('Y-m-d H:i') }}">R:PDel</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if ($message->trashed())
                            <button wire:click="restoreMessage({{ $message->id }})" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 p-1 rounded hover:bg-green-100 dark:hover:bg-green-700/50" title="{{__('Restore Admin Delete')}}"><flux:icon.arrow-path class="w-4 h-4 inline-block"/></button>
                            <button wire:click="forceDeleteMessage({{ $message->id }})" wire:confirm="Are you sure you want to PERMANENTLY delete this message? This action cannot be undone." class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-100 dark:hover:bg-red-700/50" title="{{__('Force Delete')}}"><flux:icon.trash class="w-4 h-4 inline-block"/></button>
                        @else
                            <button wire:click="adminSoftDeleteMessage({{ $message->id }})" wire:confirm="Are you sure you want to soft-delete this message (admin action)? This will hide it from regular views but can be restored." class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded hover:bg-yellow-100 dark:hover:bg-yellow-700/50" title="{{__('Admin Soft Delete')}}"><flux:icon.archive-box-arrow-down class="w-4 h-4 inline-block" /></button>
                            {{-- <button wire:click="forceDeleteMessage({{ $message->id }})" wire:confirm="Are you sure you want to PERMANENTLY delete this message? This action cannot be undone." class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-100 dark:hover:bg-red-700/50" title="{{__('Force Delete')}}"><flux:icon.trash class="w-4 h-4 inline-block"/></button> --}}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ __('No messages found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>

    {{-- Modal for editing user (ban functionality) --}}
    <livewire:admin.users.edit-user-modal />
</div>