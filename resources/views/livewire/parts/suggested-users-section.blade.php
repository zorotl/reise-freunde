{{-- resources/views/livewire/suggested-users-section.blade.php --}}
{{-- This component displays suggested users to follow. --}}
<section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Suggested Users') }}</h2>
    @if($suggestedUsers->count() > 0)
    <ul class="space-y-4">
        @foreach($suggestedUsers as $suggested)
        <li wire:key="suggest-{{ $suggested->id }}" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                {{-- User Avatar Placeholder --}}
                <span class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                    <span
                        class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300 text-xs">{{
                        $suggested->initials() }}</span>
                </span>
                <div>
                    <a href="{{ route('user.profile', $suggested->id) }}"
                        class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:underline" wire:navigate>{{
                        $suggested->name }}</a>
                    @if (isset($suggested->shared_hobbies_count) && isset($suggested->shared_travel_styles_count) &&
                    ($suggested->shared_hobbies_count + $suggested->shared_travel_styles_count > 0))
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $suggested->shared_hobbies_count + $suggested->shared_travel_styles_count }} shared
                        interest(s)
                    </p>
                    @endif
                </div>
            </div>
            {{-- Button to trigger the follow action on the parent component --}}
            <button wire:click="followUser({{ $suggested->id }})" wire:loading.attr="disabled"
                wire:target="followUser({{ $suggested->id }})"
                class="inline-flex items-center px-2.5 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                <span wire:loading wire:target="followUser({{ $suggested->id }})" class="mr-1 -ml-0.5">
                    <flux:icon.loading />
                </span>
                {{ $suggested->isPrivate() ? __('Request') : __('Follow') }}
            </button>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No suggestions right now. Add more hobbies and travel
        styles to your profile!') }}</p>
    @endif
</section>