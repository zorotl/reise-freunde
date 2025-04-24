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

    {{-- Add/Edit Form --}}
    <div class="mb-4 p-4 bg-gray-100 dark:bg-zinc-700 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
            {{ $editingTravelStyleId ? __('Edit Travel Style') : __('Add New Travel Style') }}
        </h3>
        <form wire:submit.prevent="saveTravelStyle">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-grow">
                    <label for="name" class="sr-only">{{ __('Travel Style Name') }}</label>
                    <input type="text" wire:model="name" id="name" placeholder="{{ __('Travel Style Name') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex-shrink-0 flex gap-2">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ $editingTravelStyleId ? __('Save Changes') : __('Add Travel Style') }}
                    </button>
                    @if ($editingTravelStyleId)
                    <button type="button" wire:click="cancelEdit"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Cancel') }}
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Controls: Search and Per Page --}}
    <div class="mb-4 flex justify-between items-center">
        <div class="flex-1 me-4">
            <input wire:model.live="search" type="text" placeholder="{{ __('Search travel styles...') }}"
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

    {{-- Travel Style Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden overflow-x-auto"> {{-- Added overflow-x-auto
        --}}
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    {{-- Name Header - added max-w-xs --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer max-w-xs"
                        wire:click="sortBy('name')">
                        {{ __('Name') }}
                        @if ($sortField === 'name')
                        <span class="ms-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    {{-- Status Header --}}
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                    {{-- Actions Header - added width --}}
                    <th scope="col"
                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-48">
                        {{-- Adjust width as needed --}}
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($travelStyles as $travelStyle)
                <tr @if($travelStyle->trashed()) class="bg-red-100 dark:bg-red-900/50 opacity-75" @endif>
                    {{-- Name Cell - added max-w-xs and truncate/overflow classes --}}
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs overflow-hidden text-ellipsis">
                        {{ $travelStyle->name }}
                    </td>
                    {{-- Status Cell --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if($travelStyle->trashed())
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">{{
                            __('Soft Deleted') }}</span>
                        @else
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">{{
                            __('Active') }}</span>
                        @endif
                    </td>
                    {{-- Actions Cell - added width --}}
                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium w-48"> {{-- Adjust width as
                        needed --}}
                        {{-- Action Buttons --}}
                        @if ($editingTravelStyleId !== $travelStyle->id) {{-- Hide Edit button if currently editing this
                        item --}}
                        <button wire:click="editTravelStyle({{ $travelStyle->id }})"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 me-3">{{
                            __('Edit') }}</button>
                        @endif

                        @if($travelStyle->trashed())
                        {{-- Show restore and force delete options if soft deleted --}}
                        <button wire:click="restoreTravelStyle({{ $travelStyle->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to restore this travel style?') }}')"
                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600 me-3">{{
                            __('Restore') }}</button>
                        <button wire:click="forceDeleteTravelStyle({{ $travelStyle->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to permanently delete this travel style? This action cannot be undone.') }}')"
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">{{
                            __('Force Delete') }}</button>
                        @else
                        {{-- Show soft delete option if not soft deleted --}}
                        <button wire:click="softDeleteTravelStyle({{ $travelStyle->id }})"
                            onclick="return confirm('{{ __('Are you sure you want to soft delete this travel style?') }}')"
                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600">{{
                            __('Soft Delete') }}</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ __('No travel styles found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $travelStyles->links() }}
    </div>

</div>