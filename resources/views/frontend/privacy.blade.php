<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="antialiased bg-gray-100 dark:bg-neutral-900">
    <header class="bg-white dark:bg-neutral-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
                wire:navigate>
                <x-app-logo />
            </a>
            <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Privacy Policy') }}
            </h1>
        </div>
    </header>
    <main>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
                <p class="text-lg text-gray-600 dark:text-gray-400">Placeholder for future Privacy Policy details.</p>
            </div>
        </div>
    </main>
    <x-footer />
    <x-cookie-banner />
</body>
</html>
