<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="min-h-screen bg-cover bg-center font-sans text-gray-900 antialiased"
      style="background-image: url('{{ asset('images/background-login.png') }}')">

<div class="flex items-center justify-center min-h-screen px-4">
    <div class="bg-white shadow-lg rounded-2xl flex flex-col md:flex-row overflow-hidden max-w-3xl w-full">
        
        <!-- Left side: Login Form -->
        <div class="w-full md:w-1/2 p-6 md:p-14">
            <!-- Logo -->
            <div class="mb-6 flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" alt="CookEase Logo" class="h-10 w-auto">
                <span class="text-xl font-bold text-gray-800">CookEase</span>
            </div>

            <!-- Welcome Message -->
            <div class="text-center cursor-default">
                <div class="text-2xl text-red-600 font-semibold mb-1">Welcome Back 👋</div>
                <div class="text-sm text-gray-500 mb-6">- Cook with Ease -</div>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-4">
                    <input id="remember_me" type="checkbox" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
                </div>

                <!-- Forgot Password & Submit -->
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:underline mb-2 sm:mb-0" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="bg-yellow-400 text-black text-sm font-semibold px-6 py-2 rounded hover:bg-yellow-500 transition">
                        Login
                    </button>
                </div>
                <div class="mt-4"></div>

<!-- Social Login Buttons (Tighter Gap) -->
<div class="text-center mt-8 space-y-3">
    <a href="{{ url('auth/google') }}" class="flex items-center justify-center text-black text-sm font-medium px-6 py-2 gap-3">
        <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="w-5 h-5 object-contain">
        <span>Login with Google</span>
    </a>
    <a href="{{ url('auth/facebook') }}" class="flex items-center justify-center text-black text-sm font-medium px-4 py-2 gap-2">
        <img src="{{ asset('images/Facebook-Logo.png') }}" alt="Facebook" class="w-5 h-5 object-contain">
        <span>Login with Facebook</span>
    </a>
</div>

                <!-- Register Link -->
                <div class="text-center mt-6">
                    <a href="{{ route('register') }}" class="border font-medium rounded-lg text-xs px-5 py-2.5">
                        No have an account? Create New
                    </a>
                </div>
            </form>
        </div>

        <!-- Right side: Image + Caption -->
        <div class="hidden md:flex md:w-1/2 flex-col justify-center items-center p-6 bg-gray-100">
            <img src="{{ asset('images/login-side.png') }}" alt="Chef"
                 class="w-52 h-62 object-cover rounded-xl shadow-md" />
            <div class="px-8 text-center">
                <div class="text-lg font-medium text-black mt-4 font-serif">
                    Cook smarter, live tastier
                </div>
                <p class="text-xs text-gray-600">
                    Discover the all-in-one app for effortless recipe saving, smart meal planning, seamless grocery shopping, and sharing your kitchen creations.
                </p>
            </div>
        </div>

    </div>
</div>

</body>
</html>
