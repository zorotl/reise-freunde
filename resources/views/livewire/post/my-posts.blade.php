<div>

    @php
    $now = \Carbon\Carbon::now();
    @endphp

    <div class="space-y-6">
        @foreach ($entries as $entry)
        <div class="rounded-xl shadow-md overflow-hidden 
                    @if (!$entry->expiry_date || $entry->expiry_date->lessThan($now)) bg-gray-300 
                    @elseif (!$entry->is_active) bg-red-300 
                    @else bg-white dark:bg-neutral-700
                    @endif">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-400 mb-2">
                    @if ( !$entry->is_active )
                    <span class="text-red-900 font-bold">[Inactive]</span>
                    @elseif ($entry->expiry_date && $entry->expiry_date->lessThan($now))
                    <span class="text-red-900 font-bold">[Expired]</span>
                    @endif
                    {{ $entry->title }}
                </h3>
                <p class="text-gray-700 dark:text-gray-100 leading-relaxed">{{ $entry->content }}</p>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <div>
                        <span>Posted {{ $entry->created_at->diffForHumans() }}</span>
                        @if ($entry->expiry_date)
                        <span class="mx-1">•</span>
                        <span>Expires {{ $entry->expiry_date->format('d.m.Y H:i') }}</span>
                        @endif
                    </div>
                    @if (auth()->id() === $entry->user_id)
                    <div class="space-x-2">
                        <flux:button wire:click="toggleActive({{ $entry->id }})" size="sm"
                            :variant="$entry->is_active ? 'primary' : 'filled'">
                            {{ $entry->is_active ? 'Deactivate' : 'Activate' }}
                        </flux:button>

                        <a wire:navigate href="/post/edit/{{ $entry->id }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Edit
                        </a>

                        <flux:button wire:click="deleteEntry({{ $entry->id }})" size="sm" variant="danger">
                            Delete
                        </flux:button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>