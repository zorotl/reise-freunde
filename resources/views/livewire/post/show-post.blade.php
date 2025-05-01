<div>
    @if (session()->has('error'))
    <div class="bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20">
                <title>Close</title>
                <path fill-rule="evenodd"
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.586l-2.651 3.263a1.2 1.2 0 0 1-1.697-1.697L8.303 10l-3.263-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.414l2.651-3.263a1.2 1.2 0 0 1 1.697 1.697L11.697 10l3.263 2.651a1.2 1.2 0 0 1 0 1.697z" />
            </svg>
        </span>
    </div>
    @endif

    <div class="mb-4">
        <flux:button onclick="history.back()" size="sm" variant="filled">
            Back
        </flux:button>
    </div>

    <div class="rounded-xl shadow-md overflow-hidden
    @if ($post->expiry_date && $post->expiry_date->lessThan($now)) bg-gray-300
    @elseif (!$post->is_active) bg-red-300
    @else bg-white dark:bg-neutral-700
    @endif">

        <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
            {{-- <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Test Titel') }}</h2>
            --}}
            <div class="space-y-4">
                {{-- @forelse ($entries as $post) --}}
                {{-- Include the individual post card component for each post --}}
                <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
                {{-- @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Your feed is empty. Follow some users or create your own post!') }}
                </p>
                @endforelse --}}
                {{-- Consider adding pagination or load more later for the feed --}}
            </div>
        </section>

        {{-- <div class="p-6">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-stone-400 mb-4">
                @if (!$post->is_active)
                <span class="text-red-900 font-bold">[Inactive]</span>
                @elseif ($post->expiry_date && $post->expiry_date->lessThan($now))
                <span class="text-red-900 font-bold">[Expired]</span>
                @endif
                {{ $post->title }}
            </h1>

            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                <div>
                    <span>From {{ $post->user->additionalInfo->username }}
                        posted {{ $post->created_at->diffForHumans() }}</span>
                    <span class="mx-1">•</span>

                    @if ($post->from_date && $post->to_date)
                    <span>Date: {{ $post->from_date->format('d.m.Y') }} - {{ $post->to_date->format('d.m.Y')
                        }}</span>
                    @endif

                    @if ($post->country || $post->city)
                    <span class="mx-1">•</span>
                    <span>Destination:
                        {{ $this->countryList[$post->country] ?? $post->country ?? '' }}
                        {{ $post->country && $post->city ? ' / ' : ''}}
                        {{ $post->city ?? '' }}
                    </span>
                    @endif
                </div>
            </div>

            <p class="text-gray-700 dark:text-gray-100 leading-relaxed">{{ $post->content }}</p>

            <div class="space-x-2 mt-3">
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="/user/profile/{{ $post->user->id }}">
                        Go to Profile
                    </a>
                </flux:button>

                @unless (auth()->id() === $post->user_id)
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="{{ route('mail.compose', [
                        'receiverId' => $post->user_id,
                        'fixReceiver' => true
                        ]) }}">
                        Write a message
                    </a>
                </flux:button>
                @endunless

                @if (auth()->id() === $post->user_id)
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="{{ route('post.edit', ['id' => $post->id, 'origin' => $show]) }}">
                        Edit
                    </a>
                </flux:button>

                <flux:button wire:click="toggleActive({{ $post->id }})" size="sm"
                    :variant="$post->is_active ? 'primary' : 'filled'">
                    {{ $post->is_active ? 'Deactivate' : 'Activate' }}
                </flux:button>

                <flux:button wire:click="deleteEntry({{ $post->id }})" size="sm" variant="danger">
                    Delete
                </flux:button>
                @endif
            </div>
        </div> --}}
    </div>
</div>