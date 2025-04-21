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
        <header class="bg-white dark:bg-neutral-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <a href="{{ route('home') }}"
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Travel Together') }}
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

        {{-- Hero Section --}}
        <section class="relative py-24 bg-cover bg-center"
            style="background-image: url('{{ asset('images/travel-hero.jpg') }}'); background-size: cover;">
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
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Ready to Connect?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
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
                    </div>
                </div>

                <livewire:post.recent-posts />
                <livewire:search />

                {{-- Travel Styles Section --}}
                <div class="py-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Explore by Travel
                        Style') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Adventure Travel') }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Explore thrilling
                                    destinations and activities.') }}</p>
                                <a href="#" class="text-indigo-500 hover:text-indigo-700 mt-2 block text-sm">{{ __('Find
                                    Adventure Partners') }}</a>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Relaxing Getaways') }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Discover peaceful
                                    retreats and unwind.') }}</p>
                                <a href="#" class="text-indigo-500 hover:text-indigo-700 mt-2 block text-sm">{{ __('Find
                                    Relaxation Buddies') }}</a>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Cultural Experiences')
                                    }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Immerse yourself in
                                    local cultures and traditions.') }}</p>
                                <a href="#" class="text-indigo-500 hover:text-indigo-700 mt-2 block text-sm">{{ __('Find
                                    Cultural Explorers') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Basic Search Form (Keep this below the Travel Styles) --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Search for
                        Adventures') }}</h2>
                    <form action="#" method="GET" class="flex items-center">
                        <input type="text" name="query" placeholder="{{ __('Enter keywords...') }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300">
                        <button type="submit"
                            class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                            {{ __('Search') }}
                        </button>
                    </form>
                </div>

                {{-- More homepage content will go here --}}

            </div>
        </main>
    </div>
</body>

</html>