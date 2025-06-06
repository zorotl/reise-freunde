<div>
    {{-- Success Message --}}
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
    <div class="mb-4 flex flex-wrap justify-between items-center gap-4"> {{-- Added flex-wrap and gap --}}
        <div class="flex-grow"> {{-- Search Input --}}
            <input wire:model.live="search" type="text" placeholder="{{ __('Search users...') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="flex items-center gap-2 flex-shrink-0"> {{-- Group buttons/selects --}}
            {{-- Add Clear Filter Button if filterUserId is set --}}
            @if ($filterUserId)
            <button wire:click="clearFilter"
                class="px-3 py-2 text-xs font-medium text-blue-600 border border-blue-600 rounded hover:bg-blue-100 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-900/50 focus:outline-none whitespace-nowrap">
                {{ __('Clear Filter (:userId)', ['userId' => $filterUserId]) }}
            </button>
            @endif

            {{-- Per Page Select --}}
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
    </div>

    {{-- User Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    {{-- Firstname Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer max-w-xs"
                        wire:click="sortBy('firstname')">
                        {{ __('Firstname') }}
                        @if ($sortField === 'firstname')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Lastname Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer max-w-xs"
                        wire:click="sortBy('lastname')">
                        {{ __('Lastname') }}
                        @if ($sortField === 'lastname')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Email Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer max-w-xs"
                        wire:click="sortBy('email')">
                        {{ __('Email') }}
                        @if ($sortField === 'email')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Username Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider max-w-xs">
                        {{ __('Username') }}
                    </th>
                    {{-- Roles Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Roles') }}
                    </th>
                    {{-- Banned Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Banned') }}
                    </th>
                    {{-- Actions Header - added w-40 --}}
                    <th scope="col"
                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-40">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($users as $user)
                <tr @if($user->trashed()) class="bg-red-100 dark:bg-red-900/50 opacity-75" @endif>
                    {{-- Firstname Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium max-w-xs overflow-hidden text-ellipsis">
                        <a href="{{ route('user.profile', $user->id) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 me-3">
                            {{ $user->firstname }}</a>
                    </td>
                    {{-- Lastname Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium max-w-xs overflow-hidden text-ellipsis">
                        <a href="{{ route('user.profile', $user->id) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 me-3">
                            {{ $user->lastname }}</a>
                    </td>
                    {{-- Email Cell --}}
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        {{ $user->email }}
                    </td>
                    {{-- Username Cell --}}
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                        {{ $user->additionalInfo->username ?? '-' }}
                    </td>
                    {{-- Roles Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if($user->grant)
                        @if($user->grant->is_admin) <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">{{
                            __('Admin') }}</span> @endif
                        @if($user->grant->is_moderator) <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">{{
                            __('Moderator') }}</span> @endif
                        @endif
                    </td>
                    {{-- Banned Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if($user->grant && $user->grant->is_banned)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">{{
                            __('Banned') }}</span>
                        @if($user->grant->is_banned_until)
                        <br><span class="text-xs">{{ __('Until:') }} {{ $user->grant->is_banned_until->format('Y-m-d')
                            }}</span>
                        @endif
                        @endif
                    </td>
                    {{-- Actions Cell - added w-40 --}}
                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium w-40">
                        {{-- Action Buttons --}}
                        <button wire:click="$dispatch('openEditModal', { userId: {{ $user->id }} })"
                            class="px-2 py-1 text-xs font-semibold text-indigo-600 border border-indigo-600 bg-white rounded hover:bg-indigo-100 dark:bg-gray-900 dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                            {{ __('Edit') }}
                        </button>

                        @if($user->trashed())
                        <button wire:click="restoreUser({{ $user->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to restore this user?') }}')"
                            class="px-2 py-1 text-xs font-semibold text-green-600 border border-green-600 bg-white rounded hover:bg-green-100 dark:bg-gray-900 dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500/10">
                            {{ __('Restore') }}
                        </button>
                        <button wire:click="forceDeleteUser({{ $user->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to permanently delete this user? This action cannot be undone.') }}')"
                            class="px-2 py-1 text-xs font-semibold text-red-600 border border-red-600 bg-white rounded hover:bg-red-100 dark:bg-gray-900 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500/10">
                            {{ __('Force Delete') }}
                        </button>
                        @else
                        <button wire:click="softDeleteUser({{ $user->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to soft delete this user?') }}')"
                            class="px-2 py-1 text-xs font-semibold text-yellow-600 border border-yellow-600 bg-white rounded hover:bg-yellow-100 dark:bg-gray-900 dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500/10">
                            {{ __('Soft Delete') }}
                        </button>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ __('No users found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- Include the Edit User Modal component --}}
    <livewire:admin.users.edit-user-modal />

</div>