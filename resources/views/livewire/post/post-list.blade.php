<div>
    <div class="mb-5 flex justify-between items-center">
        @if ($show === 'all')
        <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">Find your travel buddy</h2>
        @elseif ($show === 'my')
        <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">My Posts</h2>
        @endif
        <a wire:navigate href="{{ route('post.create') }}"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Create New Entry
        </a>
    </div>
    @unless ($show === 'my')
    <div>
        <livewire:search />
    </div>
    @endunless



    <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Test Titel') }}</h2>
        <div class="space-y-4">
            @forelse ($entries as $post)
            {{-- Include the individual post card component for each post --}}
            {{-- Pass the individual post object to the PostCardSection component --}}
            <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Your feed is empty. Follow some users or create your own post!') }}
            </p>
            @endforelse
            {{-- Consider adding pagination or load more later for the feed --}}
        </div>
    </section>



    {{-- <div class="space-y-6">
        @foreach ($entries as $entry)
        <div class="rounded-xl shadow-md overflow-hidden
       @if ($show === 'my' && (!$entry->expiry_date || $entry->expiry_date->lessThan($now))) bg-gray-300
       @elseif ($show === 'my' && !$entry->is_active) bg-red-300
       @else bg-white dark:bg-neutral-700
       @endif">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-400">
                    @if ($show === 'my' && !$entry->is_active)
                    <span class="text-red-900 font-bold">[Inactive]</span>
                    @elseif ($entry->expiry_date && $entry->expiry_date->lessThan($now))
                    <span class="text-red-900 font-bold">[Expired]</span>
                    @endif

                    <a href="{{ route('post.single', ['post' => $entry->id]) }}">{{ $entry->title }}</a>
                </h3>

                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <div>
                        @unless ($show === 'my')
                        <span>
                            From
                            <a wire:navigate href="/user/profile/{{ $entry->user->id }}"
                                class="text-blue-600 dark:text-blue-500 hover:underline">
                                {{ $entry->user->additionalInfo->username }}
                            </a>
                            posted {{ $entry->created_at->diffForHumans() }}
                        </span>
                        <span class="mx-1">•</span>
                        @endunless

                        @if ($entry->from_date && $entry->to_date)
                        <span>Date: {{ $entry->from_date->format('d.m.Y') }} - {{ $entry->to_date->format('d.m.Y')
                            }}</span>
                        @endif

                        @if ($entry->country || $entry->city)
                        <span class="mx-1">•</span>
                        <span>Destination:
                            {{ $this->countryList[$entry->country] ?? $entry->country ?? '' }}
                            {{ ($entry->country && $entry->city) ? ' / ' : '' }}
                            {{ $entry->city ?? '' }}
                        </span>
                        @endif
                    </div>
                </div>

                <p class="text-gray-700 dark:text-gray-100 leading-relaxed">{{ $entry->content }}</p>

                <div class="space-x-2 mt-3">
                    <flux:button size="sm" variant="outline">
                        <a wire:navigate href="/user/profile/{{ $entry->user->id }}">
                            Go to Profile
                        </a>
                    </flux:button>

                    @unless ($show === 'my' || auth()->id() === $entry->user_id)
                    <flux:button size="sm" variant="outline">
                        <a wire:navigate href="{{ route('mail.compose', [
                            'receiverId' => $entry->user_id, 
                            'fixReceiver' => true
                            ]) }}">
                            Write a message
                        </a>
                    </flux:button>
                    @endunless

                    @if (auth()->id() === $entry->user_id)
                    <flux:button size="sm" variant="outline">
                        <a wire:navigate href="{{ route('post.edit', ['id' => $entry->id, 'origin' => $show]) }}">
                            Edit
                        </a>
                    </flux:button>

                    <flux:button wire:click="toggleActive({{ $entry->id }})" size="sm"
                        :variant="$entry->is_active ? 'primary' : 'filled'">
                        {{ $entry->is_active ? 'Deactivate' : 'Activate' }}
                    </flux:button>

                    <flux:button wire:click="deleteEntry({{ $entry->id }})" size="sm" variant="danger">
                        Delete
                    </flux:button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div> --}}
</div>