<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\Message;
use App\Models\Hobby;
use App\Models\TravelStyle;
use App\Models\UserVerification;
use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};
use Illuminate\Support\Collection;

new 
#[Layout('components.layouts.admin')]
#[Title('Admin - Dashboard')]
#[Middleware('auth', 'admin_or_moderator')]
class extends Component {
    public $userCount;
    public $postCount;
    public $messageCount;
    public $hobbyCount;
    public $travelStyleCount;
    public int $postReportCount;
    public int $userReportCount;
    public int $messageReportCount;
    public int $pendingCount = 0;
    public int $pendingVerifications = 0;

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
        
        $this->postReportCount = Report::where('reportable_type', Post::class)->where('status', 'pending')->count();
        $this->userReportCount = Report::where('reportable_type', User::class)->where('status', 'pending')->count();
        $this->messageReportCount = Report::where('reportable_type', Message::class)->where('status', 'pending')->count();

        // Fetch recent items
        $this->recentUsers = User::latest()->take(5)->get();
        // Eager load user for recent posts
        $this->recentPosts = Post::with('user')->latest()->take(5)->get();
        // Eager load sender and receiver for recent messages
        $this->recentMessages = Message::with(['sender', 'receiver'])->latest()->take(5)->get();
        // Count pending users
        $this->pendingCount = User::where('status', 'pending')->whereNotNull('email_verified_at')->count();
        // Count pending verifications
        $this->pendingVerifications = UserVerification::where('status', 'pending')->count();
    }
}
?>

<div>
    {{-- Admin Dashboard Title --}}
    <h1 class="text-2xl font-semibold mb-6">{{ __('Admin Dashboard') }}</h1>

    {{-- Tasks to complete --}}
    <h2 class="text-xl font-semibold mb-4">{{ __('Tasks to complete') }}</h2>

    {{-- User --}}
    <h3 class="text-l font-semibold mb-4">{{ __('User') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.user-approvals') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 border border-orange-300 rounded-lg shadow-md p-3 hover:shadow-lg hover:bg-orange-50 dark:hover:bg-orange-950 transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Pending User Approvals') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingCount }}</p>
        </a>
        <a href="{{ route('admin.verifications') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 border border-orange-300 rounded-lg shadow-md p-3 hover:shadow-lg hover:bg-orange-50 dark:hover:bg-orange-950 transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('User Verifications') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingVerifications }}</p>
        </a>
    </div>

    {{-- Reports --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Reports') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.reports', ['reportType' => 'post']) }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 border border-orange-300 rounded-lg shadow-md p-3 hover:shadow-lg hover:bg-orange-50 dark:hover:bg-orange-950 transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Post Reports') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $postReportCount }}</p>
        </a>

        <a href="{{ route('admin.reports', ['reportType' => 'user']) }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 border border-orange-300 rounded-lg shadow-md p-3 hover:shadow-lg hover:bg-orange-50 dark:hover:bg-orange-950 transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('User Reports') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $userReportCount }}</p>
        </a>

        <a href="{{ route('admin.reports', ['reportType' => 'message']) }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 border border-orange-300 rounded-lg shadow-md p-3 hover:shadow-lg hover:bg-orange-50 dark:hover:bg-orange-950 transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Message Reports') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $messageReportCount }}</p>
        </a>
    </div>


    {{-- Statistics --}}
    <h2 class="text-xl font-semibold mb-4">{{ __('Statistics') }}</h2>

    {{-- User --}}
    <h3 class="text-l font-semibold mb-4">{{ __('User') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.users') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Total Users') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $userCount }}</p>
        </a>
        <a href="{{ route('admin.confirmations') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Real-World Confirmations') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\UserConfirmation::where('status', 'pending')->count() }}</p>
        </a>
    </div>

    {{-- Posts --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Posts') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.posts') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Total Posts') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $postCount }}</p>
        </a>
    </div>

    


    {{-- Mails --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Mails') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.messages') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Total Messages') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $messageCount }}</p>
        </a>
    </div>

    
    {{-- Hobbies and Travel Styles --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Hobbies and Travel Styles') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.hobbies') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Total Hobbies') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $hobbyCount }}</p>
        </a>

        <a href="{{ route('admin.travel-styles') }}" wire:navigate
            class="block bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 hover:shadow-lg transition">
            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Total Travel Styles') }}</h2>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $travelStyleCount }}</p>
        </a>
    </div>

    {{-- Monitoring --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Monitoring') }}</h3>
    {{-- Route Conflicts Widged --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <livewire:admin.widgets.route-conflicts />
        {{-- other widgets here --}}
    </div>

    {{-- Last Actions --}}
    <h3 class="text-l font-semibold mb-4">{{ __('Last Actions') }}</h3>
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