@if (!request()->cookie('cookie_consent'))
<div id="cookie-banner" class="fixed bottom-0 inset-x-0 bg-gray-800 text-white p-4 hidden z-50">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
        <span>
            {{ __('This site uses cookies to improve your experience. By using our site, you agree to our') }}
            <a href="{{ route('cookies') }}" class="underline">{{ __('Cookie Policy') }}</a>.
        </span>
        <button id="cookie-accept" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            {{ __('Accept') }}
        </button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const banner = document.getElementById('cookie-banner');
        if (!document.cookie.split('; ').some(row => row.startsWith('cookie_consent='))) {
            banner.classList.remove('hidden');
        }
        const accept = document.getElementById('cookie-accept');
        if (accept) {
            accept.addEventListener('click', function () {
                const expires = new Date();
                expires.setFullYear(expires.getFullYear() + 1);
                document.cookie = 'cookie_consent=1; expires=' + expires.toUTCString() + '; path=/';
                banner.remove();
            });
        }
    });
</script>
@endif
