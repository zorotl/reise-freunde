<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Hobby;
use App\Models\TravelStyle;
use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware};;

new 
#[Layout('components.layouts.admin')]
#[Middleware('auth', 'admin_or_moderator')]
class extends Component {    
    public $userCount; 
    public $postCount;
    public $messageCount;
    public $hobbyCount;
    public $travelStyleCount;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->userCount = User::count();
        $this->postCount = Post::count();
        $this->messageCount = Message::count();
        $this->hobbyCount = Hobby::count();
        $this->travelStyleCount = TravelStyle::count();
    }    
}; ?>

<div>
    {{-- Admin Dashboard Title --}}
    <h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>

    {{-- Basic Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total Users Card (now a link) --}}
        <a href="{{ route('admin.users') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition"> {{-- Added link
            and hover effect --}}
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Users') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $userCount }}</p>
        </a>

        {{-- Total Posts Card (now a link) --}}
        <a href="{{ route('admin.posts') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition"> {{-- Added link
            and hover effect --}}
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Posts') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $postCount }}</p>
        </a>

        {{-- Total Messages Card (now a link) --}}
        <a href="{{ route('admin.messages') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition"> {{-- Added link
            and hover effect --}}
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Messages') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $messageCount }}</p>
        </a>

        {{-- Total Hobbies Card (now a link) --}}
        <a href="{{ route('admin.hobbies') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition"> {{-- Added link
            and hover effect --}}
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Hobbies') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $hobbyCount }}</p>
        </a>

        {{-- Total Travel Styles Card (now a link) --}}
        <a href="{{ route('admin.travel-styles') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition"> {{-- Added link
            and hover effect --}}
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Travel Styles') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $travelStyleCount }}</p>
        </a>
    </div>

    {{-- More dashboard content can be added here later --}}

</div>