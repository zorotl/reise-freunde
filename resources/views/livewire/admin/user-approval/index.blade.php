<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">{{ __('Pending User Approvals') }}</h1>

    @if ($users->isEmpty())
        <p class="text-gray-600">{{ __('No pending users at the moment.') }}</p>
    @else
        <div class="overflow-auto rounded-lg border border-gray-200 shadow-md">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Name') }}</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Email') }}</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Registered') }}</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Email Verified') }}</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-2 text-gray-600">
                                {{ $user->email_verified_at ? $user->email_verified_at->diffForHumans() : __('Not yet') }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <button wire:click="approve({{ $user->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                        {{ __('Approve') }}
                                    </button>
                                    <button wire:click="reject({{ $user->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                        {{ __('Reject') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
