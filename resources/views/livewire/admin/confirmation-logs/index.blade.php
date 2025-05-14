<div class="max-w-6xl mx-auto py-12 px-1">
    <h1 class="text-3xl font-bold mb-6">{{ __('Admin Bürgschaft Logs') }}</h1>

    @if ($logs->isEmpty())
        <p class="text-gray-600">{{ __('No log entries found.') }}</p>
    @else
        <div class="overflow-auto border rounded shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ __('Admin') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Action') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('From → To') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('Time') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-4 py-2">{{ $log->admin->name }}</td>
                            <td class="px-4 py-2">
                                @if ($log->action === 'approved')
                                    <span class="text-green-700 font-medium">Approved</span>
                                @else
                                    <span class="text-red-700 font-medium">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                {{ $log->confirmation->requester->name }}
                                →
                                {{ $log->confirmation->confirmer->name }}
                            </td>
                            <td class="px-4 py-2 text-gray-600">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
