<footer class="bg-white dark:bg-neutral-800 border-t border-gray-200 dark:border-neutral-700">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500 dark:text-gray-400 text-sm flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
            &copy; {{ date('Y') }} {{ config('app.name', 'Travel Together') }}. {{ __('All rights reserved.') }}
        </div>
        <nav class="space-x-4">
            <a target="_blank" href="{{ route('imprint') }}" class="hover:underline">
                {{__('Imprint')}}
            </a>
            <a target="_blank" href="{{ route('privacy') }}" class="hover:underline">
                {{__('Privacy Policy')}}
            </a>
            <a target="_blank" href="{{ route('terms') }}" class="hover:underline">
                {{__('Terms & Conditions')}}
            </a>
            <a target="_blank" href="{{ route('cookies') }}" class="hover:underline">
                {{__('Cookies')}}
            </a>
        </nav>
        <div class="w-full sm:w-auto">
            <a href="mailto:info@travel-friends.ch" class="hover:underline mr-3">
                {{ __('Contact us') }}
            </a>
            <a href="{{ route('bug-report') }}" class="hover:underline">
                {{ __('Report a bug') }}
            </a>
        </div>
    </div>
</footer>
