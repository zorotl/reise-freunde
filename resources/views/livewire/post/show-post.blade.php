<div>
    <div class="rounded-xl shadow-md overflow-hidden bg-white dark:bg-neutral-700">
        <div class="p-6">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-stone-400 mb-4">
                @if (!$post->is_active)
                {{-- <span class="text-green-500 font-bold">[Active]</span> --}}
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
                    <span>Date: {{ $post->from_date->format('d.m.Y') }} - {{ $post->to_date->format('d.m.Y') }}</span>
                    <span class="mx-1">•</span>
                    @endif
                    @if ($post->country || $post->city)
                    <span>Destination: {{ $post->country ?? '' }}{{ $post->country && $post->city ? ' / ' : '' }}{{
                        $post->city ?? '' }}</span>
                    @endif
                </div>
            </div>

            <p class="text-gray-700 dark:text-gray-100 leading-relaxed mb-4">{{ $post->content }}</p>

            <div class="space-x-2 mt-3">
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="/user/profile/{{ $post->user->id }}">
                        Go to Profile
                    </a>
                </flux:button>

                @if (auth()->check() && auth()->id() !== $post->user_id)
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="{{ route('mail.compose', [
                            'receiverId' => $post->user_id,
                            'fixReceiver' => true
                            ]) }}">
                        Write a message
                    </a>
                </flux:button>
                @endif

                @if (auth()->check() && auth()->id() === $post->user_id)
                <flux:button size="sm" variant="outline">
                    <a wire:navigate href="/post/edit/{{ $post->id }}">
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
        </div>
    </div>
</div>