<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find Your Travel Buddy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-neutral-900">
        {{-- Header (No major changes needed here) --}}
        <header class="bg-white dark:bg-neutral-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <a href="{{ route('home') }}"
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Travel Together') }}
                </a>

                <x-language-switcher />

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

        {{-- Hero Section (Slight text/button size increase) --}}
        <section class="relative py-24 bg-cover bg-center"
            style="background-image: url('{{ asset('images/travel-hero.jpg') }}'); background-size: cover;">
            <div class="absolute inset-0 bg-black opacity-60"></div>
            <div class="container mx-auto text-center relative z-10 px-4">
                <div class="hero-content">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{-- Adjusted text size --}}
                        {{ __('Explore the World. Find Your Companion.') }}
                    </h1>
                    <p class="text-lg text-gray-300 mb-8 max-w-2xl mx-auto">
                        {{ __('Connect with like-minded travelers and plan your next adventure together.') }}
                    </p>
                    <a href="{{ route('post.show') }}"
                        class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-full transition duration-300 ease-in-out text-lg">
                        {{-- Adjusted text size --}}
                        {{ __('Find Travel Buddies Now') }}
                    </a>
                </div>
            </div>
        </section>

        <main>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                {{-- "Ready to Connect?" Section (Increased bottom margin) --}}
                <div class="mb-12 text-center"> {{-- Increased margin --}}
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Ready to Connect?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
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
                        @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Register Now') }}
                        </a>
                        @endguest
                    </div>
                </div>

                {{-- Search Component Wrapper --}}
                <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6 mb-12"> {{-- Added wrapper --}}
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-400 mb-4">{{ __('Quick Post Search')
                        }}</h3>
                    <livewire:search />
                </div>

                {{-- Recent Posts Component --}}
                <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6 mb-12"> {{-- Added wrapper --}}
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-400 mb-4">{{ __('Latest Travel
                        Posts')
                        }}</h3>
                    <livewire:post.recent-posts />
                </div>

                {{-- Section 1: How it Works --}}
                <section class="py-16 bg-gray-50 dark:bg-neutral-850 rounded-lg my-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">{{--
                            Increased bottom margin --}}
                            {{ __('How It Works') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center"> {{-- Increased gap
                            --}}
                            <div>
                                <flux:icon.user-plus class="mx-auto h-12 w-12 text-indigo-500 mb-4" />
                                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('1. Create
                                    Profile') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ __("Sign up and tell us about your travel
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
                                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('3. Connect
                                    & Travel') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ __("Use our secure messaging system to
                                    plan your trip together.") }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section 2: Feature Spotlight --}}
                <section class="py-16">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">{{
                            __('Discover Our Features') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {{-- Feature 1: Detailed Search --}}
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <flux:icon.adjustments-horizontal class="h-10 w-10 text-green-500" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">{{
                                        __('Advanced Search & Filters') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Find exactly who you're looking
                                        for. Filter users by age, nationality, travel style, and destination.") }}</p>
                                </div>
                            </div>
                            {{-- Feature 2: Secure Messaging --}}
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <flux:icon.chat-bubble-left-right class="h-10 w-10 text-blue-500" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">{{ __('Direct
                                        Messaging') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Connect safely and directly with
                                        potential travel partners through our integrated messaging system.") }}</p>
                                </div>
                            </div>
                            {{-- Feature 3: Customizable Profiles --}}
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <flux:icon.user-circle class="h-10 w-10 text-purple-500" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">{{ __('Rich
                                        User Profiles') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __("Showcase your personality, travel
                                        preferences, hobbies, and past trips to find compatible companions.") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section 3: Why Join? --}}
                <section class="py-16 bg-gray-50 dark:bg-neutral-850 rounded-lg my-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">{{ __('Why
                            Travel Together?') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"> {{-- Changed to 4 columns
                            --}}
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition">
                                <flux:icon.currency-dollar class="h-8 w-8 text-green-500 mb-3" />
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Share
                                    Costs') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __("Reduce costs for
                                    accommodation, transport, and activities.") }}</p>
                            </div>
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition">
                                <flux:icon.shield-check class="h-8 w-8 text-blue-500 mb-3" />
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Safety in
                                    Numbers') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __("Explore new places more
                                    comfortably with a buddy.") }}</p>
                            </div>
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition">
                                <flux:icon.camera class="h-8 w-8 text-purple-500 mb-3" />
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Shared
                                    Experiences') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __("Create lasting memories and
                                    share unforgettable moments.") }}</p>
                            </div>
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition">
                                <flux:icon.users class="h-8 w-8 text-pink-500 mb-3" />
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('New
                                    Friendships') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __("Meet people worldwide and
                                    forge friendships based on travel.") }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section 4: Community Focus (Placeholder Testimonials) --}}
                <section class="py-16">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">{{
                            __('Join Our Growing Community') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            {{-- Image Placeholder --}}
                            <div>
                                {{-- You can replace this with an actual image later --}}
                                <div
                                    class="aspect-video bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center">
                                    <flux:icon.map-pin class="h-20 w-20 text-indigo-400 dark:text-indigo-600" />
                                </div>
                            </div>
                            {{-- Text Content --}}
                            <div class="space-y-4">
                                <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('Connect with thousands of
                                    adventurers planning trips just like you. From weekend city breaks to long-term
                                    backpacking journeys, find your tribe here.') }}</p>
                                <div
                                    class="italic text-gray-600 dark:text-gray-400 border-l-4 border-indigo-300 dark:border-indigo-700 pl-4 py-2">
                                    <p>"I found an amazing hiking partner for my trip to Patagonia through Travel
                                        Together! We had an incredible time and shared so many great moments."</p>
                                    <p class="mt-2 text-sm font-medium">- Sarah K.</p>
                                </div>
                                <div
                                    class="italic text-gray-600 dark:text-gray-400 border-l-4 border-indigo-300 dark:border-indigo-700 pl-4 py-2">
                                    <p>"Sharing the driving and accommodation costs made my road trip across Europe
                                        possible. Highly recommend this platform!"</p>
                                    <p class="mt-2 text-sm font-medium">- David L.</p>
                                </div>
                                <a href="{{ route('register') }}"
                                    class="inline-block text-indigo-600 dark:text-indigo-400 hover:underline font-medium">{{
                                    __('Become a member today!') }} &rarr;</a>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section 5: Final Call to Action --}}
                <section class="text-center py-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg my-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h3 class="text-3xl font-bold text-white mb-4">{{ __('Ready for Your Next Adventure?') }}</h3>
                        <p class="text-indigo-100 mb-8 max-w-xl mx-auto">{{ __('Don\'t let travelling alone hold you
                            back. Find your perfect travel companion and start exploring the world together.') }}</p>
                        @guest
                        <a href="{{ route('register') }}"
                            class="inline-block bg-white hover:bg-gray-100 text-indigo-600 font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out shadow-md">
                            {{ __('Sign Up for Free') }}
                        </a>
                        @else
                        <a href="{{ route('post.create') }}"
                            class="inline-block bg-white hover:bg-gray-100 text-indigo-600 font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out shadow-md">
                            {{ __('Share Your Travel Plans') }}
                        </a>
                        @endguest
                    </div>
                </section>

            </div> {{-- End max-w-7xl --}}
        </main>

        {{-- Optional Footer --}}
        <footer class="bg-white dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700 mt-12">
            <div
                class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'Travel Together') }}. {{ __('All rights reserved.') }}
                {{-- Add other footer links if needed: Privacy Policy, Terms, etc. --}}
            </div>
        </footer>

    </div> {{-- End min-h-screen --}}
</body>

</html>