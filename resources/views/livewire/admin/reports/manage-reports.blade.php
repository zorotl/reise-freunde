<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
        {{-- Add close button --}}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        {{-- Add close button --}}
    </div>
    @endif

    {{-- Controls: Search, Status Filter, Per Page --}}
    <div class="mb-4 flex flex-wrap justify-between items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="{{ __('Search reports...') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="flex gap-4 items-center">
            <div>
                <label for="reportType" class="sr-only">{{ __('Report Type') }}</label>
                <select wire:model.live="reportType"
                    class="shadow border rounded py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="post">{{ __('Post Reports') }}</option>
                    <option value="user">{{ __('User Reports') }}</option>
                    <option value="message">{{ __('Message Reports') }}</option>
                </select>
            </div>
            <div>
                <label for="statusFilter" class="sr-only">{{ __('Status') }}</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="shadow border rounded py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="accepted">{{ __('Accepted') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
            <div>
                <label for="perPage" class="sr-only">{{ __('Per Page') }}</label>
                <select wire:model.live="perPage" id="perPage"
                    class="shadow border rounded py-2 px-3 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('post_id')">{{ __('Post') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('user_id')">{{ __('Reported By') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Reason') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('status')">{{ __('Status') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('created_at')">{{ __('Reported At') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse ($reports as $report)
                <tr wire:key="report-{{ $report->id }}">
                    {{-- Reported Target --}}
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        @if ($report->reportable instanceof \App\Models\Post)
                            <a href="{{ route('post.single', $report->reportable->id) }}" target="_blank"
                                class="text-blue-600 hover:underline dark:text-blue-400"
                                title="{{ $report->reportable->title }}">
                                {{ Str::limit($report->reportable->title, 40) }}
                            </a>
                            @if($report->reportable->trashed())
                                <span class="text-red-500 text-xs block">(Post Deleted)</span>
                            @endif
                        @elseif ($report->reportable instanceof \App\Models\User)
                            <a href="{{ route('admin.users', ['filterUserId' => $report->reportable->id]) }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                {{ $report->reportable->name }}
                            </a>
                        @elseif ($report->reportable instanceof \App\Models\Message)                            
                            <a href="{{ route('admin.messages.show', $report->reportable->id) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                            "{{ Str::limit($report->reportable->body, 40) }}""
                        </a>
                        @else
                            <span class="italic text-gray-400">{{ __('Target Deleted') }}</span>
                        @endif
                    </td>
                    {{-- Reporter --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        @if ($report->reporter)
                        <a href="{{ route('admin.users', ['filterUserId' => $report->reporter->id]) }}"
                            class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>
                            {{ $report->reporter->name }}
                        </a>
                        @else
                        {{ __('User Deleted') }}
                        @endif
                    </td>
                    {{-- Reason --}}
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-sm">
                        <div class="overflow-hidden text-ellipsis whitespace-nowrap" title="{{ $report->reason }}">
                            {{ $report->reason ?: '-' }}
                        </div>
                    </td>
                    {{-- Status --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        <span @class([ 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                            , 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'=> $report->status
                            === 'pending',
                            'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' => $report->status ===
                            'accepted',
                            'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' => $report->status ===
                            'rejected',
                            ])>
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                    {{-- Reported At --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $report->created_at->format('Y-m-d H:i') }}
                    </td>
                    {{-- Actions --}}
                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                        @if ($report->status === 'pending')
                        {{-- Show actions only for pending reports --}}
                        <button wire:click="acceptReport({{ $report->id }})"
                            onclick="return confirm('{{ __('Accept this report and soft-delete the post?') }}')"
                            class="px-2 py-1 text-xs font-semibold text-green-600 border border-green-600 bg-white rounded hover:bg-green-100 dark:bg-gray-900 dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500/10 me-1"
                            title="{{ __('Accept & Delete Post') }}">
                            <flux:icon.check /> {{-- Accept Icon --}}
                        </button>
                        <button wire:click="rejectReport({{ $report->id }})"
                            onclick="return confirm('{{ __('Reject this report?') }}')"
                            class="px-2 py-1 text-xs font-semibold text-red-600 border border-red-600 bg-white rounded hover:bg-red-100 dark:bg-gray-900 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500/10"
                            title="{{ __('Reject Report') }}">
                            <flux:icon.x-mark /> {{-- Reject Icon --}}
                        </button>
                        @else
                        {{-- Optionally show who processed it and when --}}
                        <span class="text-xs text-gray-400">
                            {{ __('Processed by') }} {{ $report->processor->name ?? 'N/A' }}<br>
                            {{ $report->processed_at?->format('Y-m-d H:i') ?? '' }}
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ __('No reports found matching criteria.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $reports->links() }}
    </div>

    @livewire('admin.users.edit-user-modal')
</div>