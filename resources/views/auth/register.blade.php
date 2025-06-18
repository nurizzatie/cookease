<x-guest-layout
    :backgroundImage="asset('images/background-food.png')"
    :sideImage="asset('images/signup-side.png')"
>
    <div class="w-full max-w-md md:max-w-xl lg:max-w-2xl bg-white p-6 md:p-10 rounded-lg mx-auto">
        <!-- Title -->
        <div class="flex items-center justify-between">
            <p class="text-lg md:text-xl font-extrabold text-left tracking-wide font-poppins">Create Account</p>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-9 w-8" />
        </div>

        <!-- OR Separator -->
        <div class="flex items-center mt-2 mb-6">
            <hr class="flex-grow border-gray-300">
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" class="sr-only" />
                <x-text-input id="name" class="block mt-1 w-full" placeholder="Full Name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" class="sr-only" />
                <x-text-input id="email" class="block mt-1 w-full" placeholder="Email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" class="sr-only" />
                <div class="relative">
                    <x-text-input id="password" class="block mt-1 w-full pr-10" placeholder="Password" type="password" name="password" required autocomplete="new-password" />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V7.5A4.5 4.5 0 0 0 7.5 7.5v3M5.25 10.5h13.5v9.75H5.25V10.5Z" />
                        </svg>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="sr-only" />
                <div class="relative">
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" placeholder="Confirm Password" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V7.5A4.5 4.5 0 0 0 7.5 7.5v3M5.25 10.5h13.5v9.75H5.25V10.5Z" />
                        </svg>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Create Button -->
            <div>
                <x-primary-button class="w-full justify-center">Create Account</x-primary-button>
            </div>

            <!-- Already have an account -->
            <p class="text-sm text-gray-600 mt-4 text-left">
                Already have an account?
                <a href="{{ route('login') }}" class="text-black hover:underline font-medium">Log In</a>
            </p>
        </form>
    </div>
</x-guest-layout>
