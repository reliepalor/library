<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - User</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @vite('resources/css/app.css')
    <!-- Figtree Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32?text=CSU">
</head>
<body class="bg-gray-100 font-sans antialiased">

        <!-- Header / Navigation -->
    <header class="bg-white/10 backdrop-blur-md shadow-lg animate-on-load fixed top-0 left-0 w-full z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <!-- Placeholder Logo (replace with CSU logo) -->
            <img src="https://via.placeholder.com/40?text=CSU" alt="CSU Library Logo" class="h-10 w-10">
            <a href="{{route('user.dashboard')}}" class="text-xl font-bold text-gray-900">CSU Library</a>
        </div>
        <div class="hidden md:flex space-x-6">
            <a href="#about" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">About</a>
            <a href="#search" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">Search Catalog</a>
            <a href="#services" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">Services</a>
        </div>
        <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center focus:outline-none">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="w-10 h-10 rounded-full object-cover" />
                @else
                    <svg class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 p-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                @endif
                <svg class="ml-2 w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
        <button class="md:hidden" id="menu-toggle" aria-label="Toggle menu">
            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>
    <!-- Mobile Menu (Hidden by Default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white/10 backdrop-blur-md text-gray-900 px-4 py-4">
        <a href="#about" class="block py-2 hover:text-csu-light-blue transition">About</a>
        <a href="#search" class="block py-2 hover:text-csu-light-blue transition">Search Catalog</a>
        <a href="#services" class="block py-2 hover:text-csu-light-blue transition">Services</a>
    </div>
</header>

    <main class="max-w-3xl mx-auto p-6 bg-white mt-6 rounded shadow">
        @if (session('status') === 'profile-updated')
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                Profile updated successfully.
            </div>
        @endif

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Profile Picture -->
            <div class="mb-4">
                <label for="profile_picture" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
                <input id="profile_picture" name="profile_picture" type="file" class="border rounded w-full p-2" />
                @error('profile_picture')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="mt-2 w-24 h-24 rounded-full object-cover">
                @endif
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="border rounded w-full p-2" />
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="border rounded w-full p-2" />
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- College -->
            <div class="mb-4">
                <label for="college" class="block text-gray-700 font-medium mb-2">College</label>
                <input id="college" name="college" type="text" value="{{ old('college', $user->college) }}" class="border rounded w-full p-2" />
                @error('college')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Year -->
            <div class="mb-4">
                <label for="year" class="block text-gray-700 font-medium mb-2">Year</label>
                <input id="year" name="year" type="text" value="{{ old('year', $user->year) }}" class="border rounded w-full p-2" />
                @error('year')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Other Info -->
            <div class="mb-4">
                <label for="other_info" class="block text-gray-700 font-medium mb-2">Other Information</label>
                <textarea id="other_info" name="other_info" rows="3" class="border rounded w-full p-2">{{ old('other_info', $user->other_info) }}</textarea>
                @error('other_info')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </main>

</body>
</html>
