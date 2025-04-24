<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Hobby;
use App\Models\TravelStyle;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Middleware;

new class extends Component {    
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
        {{-- Example Stat Card (using simple divs as FluxUI cards have issues) --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Users</h2>
            {{-- Access public properties directly --}}
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $userCount }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Posts</h2>
            {{-- Access public properties directly --}}
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $postCount }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Messages</h2>
            {{-- Access public properties directly --}}
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $messageCount }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Hobbies</h2>
            {{-- Access public properties directly --}}
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $hobbyCount }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Travel Styles</h2>
            {{-- Access public properties directly --}}
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $travelStyleCount }}</p>
        </div>
    </div>

    {{-- More dashboard content can be added here later --}}

</div>