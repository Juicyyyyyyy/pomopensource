<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="text-white/70" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" class="text-white/70" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-indigo text-blue-800/50 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-white">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="hover:underline text-sm text-white" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-4 bg-blue-800/50 text-white rounded-lg hover:bg-blue-900/50 transition">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Register Redirect Button -->
    <div class="flex items-center justify-center mt-6">
        <a href="{{ route('register') }}" class="text-sm text-white hover:underline">
            {{ __("Don't have an account? Register here") }}
        </a>
    </div>
</x-guest-layout>
