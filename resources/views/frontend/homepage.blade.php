<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find Your Travel Buddy</title> {{-- Consider making title dynamic --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-neutral-900">
        {{-- Existing Header --}}
        <header class="bg-white dark:bg-neutral-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <a href="{{ route('home') }}"
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Travel Together') }} {{-- Consider using config('app.name') --}}
                </a>
                <nav class="space-x-4">
                    <a href="{{ route('post.show') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200">{{
                        __('Travel Posts') }}</a>
                    <a href="{{ route('user.directory') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200">{{
                        __('Find Users') }}</a>
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200">{{
                        __('Dashboard') }}</a>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200">{{ __('Log
                        in') }}</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200">{{
                        __('Register') }}</a>
                    @endif
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Existing Hero Section --}}
        <section class="relative py-24 bg-cover bg-center"
            style="background-image: url('{{ asset('images/travel-hero.jpg') }}'); background-size: cover;">
            {{-- Background overlay --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>
            <div class="container mx-auto text-center relative z-10">
                <div class="hero-content">
                    <h1 class="text-4xl font-bold text-white mb-4">{{ __('Explore the World. Find Your Companion.') }}
                    </h1>
                    <p class="text-lg text-gray-300 mb-8">{{ __('Connect with like-minded travelers and plan your next
                        adventure together.') }}</p>
                    <a href="{{ route('post.show') }}"
                        class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-full transition duration-300 ease-in-out">
                        {{ __('Find Travel Buddies Now') }}
                    </a>
                </div>
            </div>
        </section>

        <main>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                {{-- Existing Content: Ready to Connect? --}}
                <div class="mb-8 text-center"> {{-- Centered this section --}}
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Ready to Connect?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto"> {{-- Constrained width --}}
                        {{ __('Browse the latest travel posts or create your own to find the perfect travel partner.')
                        }}
                    </p>
                    <div class="mt-6 space-x-4">
                        <a href="{{ route('post.show') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                            {{ __('See All Posts') }}
                        </a>
                        <a href="{{ route('user.directory') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                            {{ __('Find Users') }}
                        </a>
                        {{-- Added Register button for guests --}}
                        @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Register Now') }}
                        </a>
                        @endguest
                    </div>
                </div>

                {{-- Existing Livewire Components --}}
                <livewire:search />
                <livewire:post.recent-posts />

                {{-- <<< START: New Homepage Content>>> --}}

                    {{-- How it Works Section --}}
                    <section class="py-16 bg-gray-50 dark:bg-neutral-850 rounded-lg my-12">
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-8">{{
                                __('How It Works') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                                <div>
                                    <flux:icon.user-plus class="mx-auto h-12 w-12 text-indigo-500 mb-4" />
                                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('1.
                                        Create
                                        Profile') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Sign up and tell us about your
                                        travel
                                        styles and hobbies.") }}</p>
                                </div>
                                <div>
                                    <flux:icon.magnifying-glass class="mx-auto h-12 w-12 text-indigo-500 mb-4" />
                                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('2. Find
                                        Matches') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Browse posts or search for users
                                        with similar interests and destinations.") }}</p>
                                </div>
                                <div>
                                    <flux:icon.paper-airplane class="mx-auto h-12 w-12 text-indigo-500 mb-4" />
                                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('3.
                                        Connect
                                        & Travel') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Use our secure messaging system
                                        to
                                        plan your trip together.") }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Why Join? Section --}}
                    <section class="py-16">
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-8">{{
                                __('Why
                                Travel Together?') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow">
                                    <flux:icon.currency-dollar class="h-8 w-8 text-green-500 mb-3" />
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Share
                                        Costs') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Travelling with a companion can
                                        significantly reduce costs for accommodation, transport, and activities.") }}
                                    </p>
                                </div>
                                <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow">
                                    <flux:icon.shield-check class="h-8 w-8 text-blue-500 mb-3" />
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Safety
                                        in
                                        Numbers') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Exploring new places is often
                                        safer
                                        and more comfortable with a buddy by your side.") }}</p>
                                </div>
                                <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow">
                                    <flux:icon.camera class="h-8 w-8 text-purple-500 mb-3" />
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Shared
                                        Experiences') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Create lasting memories and share
                                        unforgettable moments with someone who shares your passion.") }}</p>
                                </div>
                                <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow">
                                    <flux:icon.users class="h-8 w-8 text-pink-500 mb-3" />
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('New
                                        Friendships') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Meet new people from around the
                                        world
                                        and forge friendships based on shared travel dreams.") }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Call to Action --}}
                    <section class="text-center py-12">
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Start Your
                            Journey
                            Today!') }}</h3>
                        @guest
                        <a href="{{ route('register') }}"
                            class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out">
                            {{ __('Sign Up for Free') }}
                        </a>
                        @else
                        <a href="{{ route('post.create') }}"
                            class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out">
                            {{ __('Create a Post') }}
                        </a>
                        @endguest
                    </section>

                    {{-- <<< END: New Homepage Content>>> --}}

            </div>
        </main>
    </div>
</body>

</html>