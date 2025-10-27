<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #1a1a1a; /* Dark background matching Lovable */
            font-family: 'Figtree', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 50%, #4b5e9e 100%); /* Adjusted gradient: gray to blue tones for library theme */
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease-out forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hover-scale {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            background-color: #4b5e9e; /* Adjusted blue on hover */
        }
        .input-focus {
            transition: border-color 0.3s ease;
        }
        .input-focus:focus {
            border-color: #4b5e9e; /* Adjusted blue focus ring */
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    <!-- Left Section: Gradient Background -->
    <div class="w-full lg:w-1/2 gradient-bg flex items-center justify-center p-6 lg:p-12">
        <div class="text-center text-white fade-in">
            <img src="{{ asset('favicon/library.png') }}" alt="Library Logo" class="w-16 h-16 rounded-full shadow-md mx-auto mb-6">
            
            <h2 class="text-3xl font-bold">CSU-G Library</h2>
            <p class="mt-2 text-gray-300">Access your library account</p>
        </div>
    </div>

    <!-- Right Section: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-[#1a1a1a] text-white">
        <div class="w-full max-w-md fade-in">
            <!-- Title -->
            <h2 class="text-3xl font-bold text-center mb-6">Sign in</h2>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 text-sm text-green-400 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                    <input id="email" name="email" type="email" required autofocus autocomplete="username"
                        value="{{ old('email') }}"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-sm text-gray-200 input-focus" />
                    @error('email')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 pr-12 text-sm text-gray-200 input-focus" />
                    <button type="button" id="togglePassword" tabindex="-1" class="absolute right-3 top-9 text-gray-400 hover:text-gray-200 focus:outline-none" style="background: none; border: none; padding: 0;">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    @error('password')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me + Forgot -->
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <span></span>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-400 hover:text-indigo-300">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Buttons -->
                <div class="space-y-3">
                    <button type="submit"
                        class="w-full px-4 py-3 bg-white text-gray-900 rounded-lg font-medium hover:bg-gray-100 hover-scale focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Sign in
                    </button>
<a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white text-gray-900 rounded-lg font-medium hover:bg-gray-100 hover-scale mt-3 duration-100">
  <img src="https://www.google.com/favicon.ico" alt="Google Icon" class="w-5 h-5 mr-2">
  Continue with Google
</a>
                    <a href="{{ route('register') }}"
                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-600 text-gray-300 rounded-lg font-medium hover:bg-gray-700 hover-scale mt-3">
                        Don't have an account? Sign up
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    let show = false;
    togglePassword.addEventListener("click", (e) => {
        e.preventDefault();
        show = !show;
        password.type = show ? "text" : "password";
        eyeIcon.innerHTML = show
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-4-9-7s4-7 9-7 9 4 9 7c0 1.306-.835 2.417-2.125 3.825M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
    });
</script>
</html>