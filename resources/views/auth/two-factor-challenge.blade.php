<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __(!$recovery ? 'Please confirm access to your account by entering the authentication code provided by authenticator app.' : 'Please confirm access to your account by entering the recovery code provided by 2FA') }}
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        @if (!$recovery)
            <!-- Authenticate Code -->
            <div class="mt-4">
                <x-input-label for="code" :value="__('Enter Code')" />

                <x-text-input id="code" inputmode='numeric' class="mt-1 block w-full" type="text" name="code"
                    autocomplete="one-time-code" />

                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @else
            <!-- Recovery Code -->
            <div class="mt-4">
                <x-input-label for="recovery_code" :value="__('Enter Code')" />

                <x-text-input id="recovery_code" class="mt-1 block w-full" type="text" name="recovery_code" autofocus
                    autocomplete="one-time-code" />

                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>
        @endif

        <div class="flex-center mt-4 flex justify-end">
            <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                href="{{ $recovery ? route('two-factor.login') : route('two-factor.login', ['recovery' => true]) }}">
                {{ __(!$recovery ? 'Use Recovery Code' : 'Use Authentication Code') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
