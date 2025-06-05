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

    {{-- Bug Report Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Reported') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('URL / Page') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Message') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('User') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Email') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($reports as $report)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $report->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400">
                            {{ $report->url }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-800 dark:text-gray-100 max-w-sm break-words">
                            {{ $report->message }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-800 dark:text-gray-100 whitespace-nowrap">
                            @if($report->user)
                                <a href="{{ route('admin.users', ['filterUserId' => $report->user->id]) }}"
                                class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                                    {{ $report->user->additionalInfo->username ?? $report->user->name }}
                                </a>
                                @if($report->user->trashed())
                                    <span class="text-xs text-red-500 dark:text-red-400">(Deleted)</span>
                                @endif
                            @else
                                <span class="italic text-gray-400 dark:text-gray-500">{{ __('Guest') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300">
                            {{ $report->email ?? '—' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @switch($report->status)
                                @case('pending')
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200">
                                        {{ __('Pending') }}
                                    </span>
                                    @break
                                @case('accepted')
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200">
                                        {{ __('Accepted') }}
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-200">
                                        {{ __('Rejected') }}
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-3 py-4 text-right text-sm font-medium space-x-2">
                            <button wire:click="accept({{ $report->id }})"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded">
                                {{ __('Accept') }}
                            </button>
                            <button wire:click="reject({{ $report->id }})"
                                class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded">
                                {{ __('Reject') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                            {{ __('No pending bug reports.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
