<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" class="text-white/70" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="text-white/70" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" class="text-white/70" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" class="text-white/70"         :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white/20 border border-white/30 rounded-lg text-white" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="hover:underline text-sm text-white" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4 bg-blue-800/50 text-white rounded-lg hover:bg-blue-900/50 transition">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
