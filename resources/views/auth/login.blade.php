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
            <h2 class="text-3xl font-bold">CSU Library</h2>
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
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-sm text-gray-200 input-focus" />
                    @error('password')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me + Forgot -->
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" id="togglePassword" class="mr-2 rounded border-gray-600 text-indigo-500 FOCUS:ring-indigo-500 bg-gray-800">
                        Show Password
                    </label>
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
                    <a href="{{ route('register') }}"
                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-600 text-gray-300 rounded-lg font-medium hover:bg-gray-700 hover-scale">
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

    togglePassword.addEventListener("change", () => {
        if(togglePassword.checked){
            password.type = "text";
        }else{
            password.type = "password";
        }
    })
</script>
</html>