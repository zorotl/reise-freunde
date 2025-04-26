<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Hobby;
use App\Models\TravelStyle;
use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware};;
use Illuminate\Support\Collection;

new 
#[Layout('components.layouts.admin')]
#[Middleware('auth', 'admin_or_moderator')]
class extends Component {
    public $userCount;
    public $postCount;
    public $messageCount;
    public $hobbyCount;
    public $travelStyleCount;

    // New properties for recent items
    public Collection $recentUsers;
    public Collection $recentPosts;
    public Collection $recentMessages;

    public function mount(): void
    {
        $this->userCount = User::count();
        $this->postCount = Post::count();
        $this->messageCount = Message::count();
        $this->hobbyCount = Hobby::count();
        $this->travelStyleCount = TravelStyle::count();

        // Fetch recent items
        $this->recentUsers = User::latest()->take(5)->get();
        // Eager load user for recent posts
        $this->recentPosts = Post::with('user')->latest()->take(5)->get();
        // Eager load sender and receiver for recent messages
        $this->recentMessages = Message::with(['sender', 'receiver'])->latest()->take(5)->get();
    }
}
?>

<div>
    {{-- Admin Dashboard Title --}}
    <h1 class="text-2xl font-semibold mb-6">{{ __('Admin Dashboard') }}</h1>

    {{-- Basic Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"> {{-- Added mb-8 for spacing below stats --}}
        {{-- Total Users Card (link) --}}
        <a href="{{ route('admin.users') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Users') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $userCount }}</p>
        </a>

        {{-- Total Posts Card (link) --}}
        <a href="{{ route('admin.posts') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Posts') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $postCount }}</p>
        </a>

        {{-- Total Messages Card (link) --}}
        <a href="{{ route('admin.messages') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Messages') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $messageCount }}</p>
        </a>

        {{-- Total Hobbies Card (link) --}}
        <a href="{{ route('admin.hobbies') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Hobbies') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $hobbyCount }}</p>
        </a>

        {{-- Total Travel Styles Card (link) --}}
        <a href="{{ route('admin.travel-styles') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Total Travel Styles') }}</h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $travelStyleCount }}</p>
        </a>
    </div>

    {{-- Recent Activity Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('Recent Users') }}</h3>
            @forelse ($recentUsers as $user)
            <div class="border-b border-gray-100 dark:border-neutral-700 pb-2 mb-2 last:border-b-0 last:pb-0 last:mb-0">
                <a href="{{ route('admin.users', ['filterUserId' => $user->id]) }}" wire:navigate
                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">{{ $user->name }}</a>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No recent users.') }}</p>
            @endforelse
        </div>

        {{-- Recent Posts --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('Recent Posts') }}</h3>
            @forelse ($recentPosts as $post)
            <div class="border-b border-gray-100 dark:border-neutral-700 pb-2 mb-2 last:border-b-0 last:pb-0 last:mb-0">
                <a href="{{ route('post.single', $post->id) }}" wire:navigate
                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">{{
                    Str::limit($post->title, 40) }}</a>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('by') }} {{ $post->user->name ?? 'N/A' }} - {{
                    $post->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No recent posts.') }}</p>
            @endforelse
        </div>

        {{-- Recent Messages --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('Recent Messages') }}</h3>
            @forelse ($recentMessages as $message)
            <div class="border-b border-gray-100 dark:border-neutral-700 pb-2 mb-2 last:border-b-0 last:pb-0 last:mb-0">
                <a href="{{ route('admin.messages.show', $message->id) }}" wire:navigate
                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">{{
                    Str::limit($message->subject, 40) }}</a> {{-- Link to admin message view --}}
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('From:') }} {{ $message->sender->name ?? 'N/A'
                    }} {{ __('To:') }} {{ $message->receiver->name ?? 'N/A' }} - {{
                    $message->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No recent messages.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- More dashboard content can be added here later --}}

</div>