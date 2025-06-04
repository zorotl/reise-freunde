<footer class="bg-white dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700 mt-12">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 dark:text-gray-400 text-sm flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
            &copy; {{ date('Y') }} {{ config('app.name', 'Travel Together') }}. {{ __('All rights reserved.') }}
        </div>
        <nav class="space-x-4">
            <a href="{{ route('imprint') }}" class="hover:underline">Imprint</a>
            <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
            <a href="{{ route('terms') }}" class="hover:underline">Terms &amp; Conditions</a>
            <a href="{{ route('cookies') }}" class="hover:underline">Cookies</a>
        </nav>
    </div>
</footer>
