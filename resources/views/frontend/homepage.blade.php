<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Find Your Travel Buddy</title>
    <meta name="description"
          content="Connect with travel companions worldwide. Find posts, users, and plan trips together.">
    <meta property="og:image"
          content="{{ asset('images/travel-hero.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-neutral-900">
        {{-- Header (No major changes needed here) --}}
        <header class="bg-white dark:bg-neutral-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <a href="{{ route('home') }}"
                   class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Travel Friends') }}
                </a>

                <x-language-switcher />

                <nav class="space-x-2 flex items-center justify-end">                 
                    @auth
                        <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm">
                        {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-800 transition text-sm">
                        {{ __('Log in') }}
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                            class="px-4 py-2 rounded-md bg-indigo-500 text-white hover:bg-indigo-700 transition text-sm">
                            {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero Section (Slight text/button size increase) --}}
        <section class="relative h-[300px] md:h-[450px] bg-center"
                 style="background-image: url('{{ asset('images/travel-hero.jpg') }}'); background-size: 100% auto;">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            <div class="relative z-10 flex flex-col justify-center items-center h-full text-center px-4">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">
                    {{ __('Explore the World. Find Your Companion.') }}
                </h1>
                <p class="text-lg md:text-xl text-gray-200 leading-relaxed max-w-2xl mb-6">
                    {{ __('Connect with like-minded travelers and plan your next adventure together.') }}
                </p>
                <a href="{{ route('post.show') }}"
                   class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-full transition duration-300 ease-in-out text-base">
                    {{ __('Find Travel Buddies Now') }}
                </a>
            </div>
        </section>

        <main>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">                

            {{-- Recent Posts Component --}}
            <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6 mb-12">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-400 mb-4">
                    {{ __('Latest Travel Posts') }}
                </h3>
                <livewire:post.recent-posts />
            </section>

            {{-- New Section: Enhanced Trust and Safety --}}
            <section class="py-16 bg-gray-100 dark:bg-neutral-800 rounded-lg my-12">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">
                        {{ __('A Community Built on Trust') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center">
                        {{-- Feature 1: Manual Verification --}}
                        <div>
                            {{-- You can find appropriate icons from FluxUI or other libraries --}}
                            <flux:icon.shield-check class="mx-auto h-12 w-12 text-green-500 mb-4" />
                            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Verified Profiles') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ __('To ensure a genuine community, all new profiles undergo a review process. This helps us prevent fake accounts and build a safer environment for everyone. You\'ll have read-only access while your profile is being verified (typically within 2 days).') }}
                            </p>
                        </div>

                        {{-- Feature 2: Bürgschaft/Vouching --}}
                        <div>
                            <flux:icon.user-group class="mx-auto h-12 w-12 text-blue-500 mb-4" /> 
                            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Real-Life Confirmations') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ __('Met someone from our community in person? Members can vouch for each other, adding an extra layer of trust and displaying a special badge on their profiles.') }}
                            </p>
                        </div>

                        {{-- Feature 3: Multiple Verification Methods --}}
                        <div>
                            <flux:icon.identification class="mx-auto h-12 w-12 text-purple-500 mb-4" /> 
                            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Show Your Authenticity') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ __('Increase your credibility by verifying your identity through various methods, such as an ID document, linking social media profiles, or writing a self-verification text. Each verified method earns you a badge!') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- "Ready to Connect?" Section (Increased bottom margin) --}}
            <section class="mb-12 p-5 text-center bg-sky-200"> {{-- Increased margin --}}
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Ready to Connect?') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ __('Browse the latest travel posts or create your own to find the perfect travel partner.') }}
                </p>
                <div class="mt-6 space-x-4">
                    <a href="{{ route('post.show') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                        {{ __('See All Posts') }}
                    </a>
                    <a href="{{ route('user.directory') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                        {{ __('Find Users') }}
                    </a>
                    @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Register Now') }}
                        </a>
                    @endguest
                </div>
            </section>

                            {{-- Section 3: Why Join? --}}
                <section class="py-16 bg-gray-50 dark:bg-neutral-850 rounded-lg my-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-gray-100 mb-10">
                            {{ __('Why Travel Together?') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <!-- Share Costs -->
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition text-center">
                                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.currency-dollar class="h-8 w-8 text-green-500" />
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ __('Share Costs') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    {{ __('Reduce costs for accommodation, transport, and activities.') }}
                                </p>
                            </div>

                            <!-- Safety in Numbers -->
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition text-center">
                                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.shield-check class="h-8 w-8 text-blue-500" />
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ __('Safety in Numbers') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    {{ __('Explore new places more comfortably with a buddy.') }}
                                </p>
                            </div>

                            <!-- Shared Experiences -->
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition text-center">
                                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.camera class="h-8 w-8 text-purple-500" />
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ __('Shared Experiences') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    {{ __('Create lasting memories and share unforgettable moments.') }}
                                </p>
                            </div>

                            <!-- New Friendships -->
                            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow hover:shadow-md transition text-center">
                                <div class="bg-pink-100 dark:bg-pink-900 p-3 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.users class="h-8 w-8 text-pink-500" />
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ __('New Friendships') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    {{ __('Meet people worldwide and forge friendships based on travel.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>            

                {{-- Section 5: Final Call to Action --}}
                <section class="text-center py-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg my-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h3 class="text-3xl font-bold text-white mb-4">{{ __('Ready for Your Next Adventure?') }}</h3>
                        <p class="text-indigo-100 mb-8 max-w-xl mx-auto">
                            {{ __('Don\'t let travelling alone hold you back. Find your perfect travel companion and start exploring the world together.') }}
                        </p>
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
        <x-footer />
        <x-cookie-banner />

    </div> {{-- End min-h-screen --}}
</body>

</html>
