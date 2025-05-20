{{-- resources/views/livewire/post/post-list.blade.php --}}
<div>
    {{-- Page Header --}}
    <div class="mb-5 flex justify-between items-center flex-wrap gap-4">
        <div>
            @if ($show === 'all')
            <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">{{__('Find your travel buddy')}}</h2>
            @elseif ($show === 'my')
            <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">{{__('My Posts')}}</h2>
            @endif
        </div>
        @auth
        <a wire:navigate href="{{ route('post.create') }}"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            {{__('Create New Entry')}}
        </a>
        @endauth
    </div>

    {{-- Search and Filter Section --}}
    @unless ($show === 'my')
    <livewire:search />
    {{-- Include the new PostFilters component --}}
    <livewire:post-filters />
    @endunless

    {{-- Results Area --}}
    <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
        <div class="space-y-4">
            @if ($entries && $entries->count() > 0)
            @foreach ($entries as $post)
            <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
            @endforeach
            <div class="mt-4">
                {{ $entries->links() }}
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                {{ __('No posts found matching your criteria.') }}
            </p>
            @endif
        </div>
    </section>
</div>