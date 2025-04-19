<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('User Profile') }}
                </h2>

                <div class="col-span-full">
                    <div class="rounded-full overflow-hidden w-24 h-24 bg-gray-300 flex items-center justify-center">
                        @if ($user->additionalInfo?->profile_picture)
                        {{-- Image upload not yet implemented, so we'll use a placeholder --}}
                        <img src="{{ asset('storage/' . $user->additionalInfo->profile_picture) }}"
                            alt="Profile Picture" class="w-full h-full object-cover">
                        @else
                        <span class="text-xl text-gray-600 dark:text-gray-400">{{ $user->initials() }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block font-medium text-gray-700 dark:text-gray-300">{{ __('Name')
                            }}</label>
                        <p id="name" class="mt-1 block w-full text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                    </div>

                    @if ($user->additionalInfo?->username)
                    <div>
                        <label for="username" class="block font-medium text-gray-700 dark:text-gray-300">{{
                            __('Username') }}</label>
                        <p id="username" class="mt-1 block w-full text-gray-900 dark:text-gray-100">{{
                            $user->additionalInfo->username }}
                        </p>
                    </div>
                    @endif

                    <div>
                        <label for="email" class="block font-medium text-gray-700 dark:text-gray-300">{{ __('Email')
                            }}</label>
                        <p id="email" class="mt-1 block w-full text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                        @if (!$user->hasVerifiedEmail())
                        <p class="mt-1 text-sm text-yellow-500 dark:text-yellow-400">
                            {{ __('Your email address is not verified.') }}
                        </p>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    @if ($user->additionalInfo?->birthday)
                    <div>
                        <label for="age" class="block font-medium text-gray-700 dark:text-gray-300">{{ __('Age')
                            }}</label>
                        <p id="age" class="mt-1 block w-full text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($user->additionalInfo->birthday)->age }}
                        </p>
                    </div>
                    @endif

                    @if ($user->additionalInfo?->nationality)
                    <div>
                        <label for="nationality" class="block font-medium text-gray-700 dark:text-gray-300">{{
                            __('Nationality')
                            }}</label>
                        <p id="nationality" class="mt-1 block w-full text-gray-900 dark:text-gray-100">{{
                            $user->additionalInfo->nationality }}</p>
                    </div>
                    @endif
                </div>

                <div class="col-span-full">
                    <label for="about_me" class="block font-medium text-gray-700 dark:text-gray-300">{{ __('About Me')
                        }}</label>
                    <textarea id="about_me"
                        class="py-1 px-2 mt-1 block w-full border-gray-300 dark:border-neutral-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-gray-300"
                        rows="5" readonly>{{ $user->additionalInfo->about_me ?? __('Not set') }}</textarea>
                </div>
            </div>
            <div
                class="px-4 py-3 bg-gray-50 dark:bg-neutral-900 text-right sm:px-6 border-t border-gray-200 dark:border-neutral-700">
                <a wire:navigate href="{{ route('settings.profile') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Edit Profile') }}
                </a>
            </div>
        </div>
    </div>
</div>