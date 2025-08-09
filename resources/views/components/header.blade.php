<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <header x-data="{ open: false }" class="bg-white/10 backdrop-blur-xl fixed top-0 left-0 w-full z-50 transition-all duration-300">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <!-- Left: Logo and Brand -->
        <div class="flex items-center space-x-3">
            <img src="/images/library.png" alt="CSU Library Logo" class="h-8 w-8 sm:h-10 sm:w-10 transition-transform duration-300 hover:scale-105">
            <a href="/" class="text-lg sm:text-xl font-semibold text-gray-800 tracking-tight">CSU Library</a>
        </div>

        <!-- Center: Navigation Links (Desktop) -->
        <div class="hidden md:flex flex-1 justify-center items-center space-x-2 lg:space-x-3">
            <a href="{{ route('user.attendance.index') }}" class="px-3 py-2 text-sm lg:text-base text-gray-900 hover:bg-white/30  rounded-lg transition-colors duration-200 hover:text-blue-700 ">Attendance</a>
            <a href="{{ route('user.books.index') }}" class="px-3 py-2 text-sm lg:text-base text-gray-900 hover:bg-white/30  rounded-lg transition-colors duration-200 hover:text-blue-700 ">Books</a>
            <a href="#services" class="px-3 py-2 text-sm lg:text-base text-gray-900 hover:bg-white/30  rounded-lg transition-colors duration-200 hover:text-blue-700 ">Services</a>
        </div>

        <!-- Right: Profile Dropdown and Auth Links (Desktop) -->
        <div class="hidden md:flex items-center space-x-2 lg:space-x-3">
            @guest
                <a href="{{ route('login') }}" class="px-3 py-2 text-sm lg:text-base text-gray-900 hover:bg-white/30  rounded-lg transition-colors duration-200 hover:text-blue-500 ">Login</a>
                <a href="{{ route('register') }}" class="px-3 py-2 text-sm lg:text-base text-gray-900 hover:bg-white/30  rounded-lg transition-colors duration-200 hover:text-blue-500 ">Register</a>
            @endguest
            @auth
                <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
                    <button 
                        @click="dropdownOpen = !dropdownOpen" 
                        class="flex items-center focus:outline-none text-gray-900 p-1 rounded-full hover:bg-white/30  transition-colors duration-200" 
                        :aria-expanded="dropdownOpen"
                        :aria-label="dropdownOpen ? 'Close profile menu' : 'Open profile menu'"
                    >
@if(optional(auth()->user())->profile_picture)
    <img src="{{ asset('storage/' . optional(auth()->user())->profile_picture) }}" alt="Profile Picture" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border border-gray-200/50 dark:border-gray-700/50" />
@else
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/20 dark:bg-gray-700/20 text-gray-600 dark:text-gray-400 p-1.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        @endif
                        <svg class="w-4 h-4 ml-1 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div 
                        x-show="dropdownOpen" 
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 scale-y-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-y-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 scale-y-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-y-95 translate-y-1"
                        class="absolute right-0 mt-2 w-48 bg-white backdrop-blur-md border border-gray-200/50 dark:border-gray-700/50 rounded-xl shadow-lg py-1 z-50 overflow-hidden"
                    >
                        <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Right: Mobile Menu Toggle -->
        <div class="md:hidden flex items-center space-x-2">
            <div class="relative" x-data="{ mobileMenuOpen: false }" @click.away="mobileMenuOpen = false">
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="flex items-center focus:outline-none text-gray-900 p-1.5 rounded-full hover:bg-white/30  transition-colors duration-200" 
                    :aria-expanded="mobileMenuOpen"
                    :aria-label="mobileMenuOpen ? 'Close menu' : 'Open menu'"
                >
@if(optional(auth()->user())->profile_picture)
    <img src="{{ asset('storage/' . optional(auth()->user())->profile_picture) }}" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover border border-gray-200/50 dark:border-gray-700/50" />
@else
                        <svg class="w-8 h-8 rounded-full bg-white/20 dark:bg-gray-700/20 text-gray-600 dark:text-gray-400 p-1.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    @endif
                    <svg class="w-4 h-4 ml-1 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div 
                    x-show="mobileMenuOpen" 
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-y-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-y-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-y-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-y-95 translate-y-1"
                    class="absolute right-0 mt-2 w-48 bg-white backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/50 rounded-xl shadow-lg py-1 z-50 overflow-hidden"
                >
                    <a href="{{ route('user.attendance.index') }}" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Attendance</a>
                    <a href="{{ route('user.books.index') }}" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Books</a>
                    <a href="#services" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Services</a>
                    @guest
                        <a href="{{ route('login') }}" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Login</a>
                        <a href="{{ route('register') }}" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Register</a>
                    @endguest
                    @auth
                        <a href="{{ route('user.profile.edit') }}" class="block px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-900 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-colors duration-200">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </header>   
</body>
</html>