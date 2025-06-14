<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Available Books - CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/favicon/library.png" />
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/heroicons@2.0.16/dist/20/outline.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom Gradient Background */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
            min-height: 100vh;
        }
        /* Smooth Scroll Behavior */
        html {
            scroll-behavior: smooth;
        }
        /* Fade-in Animation for Main Content */
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
        /* Hover Scale Effect */
        .hover-scale {
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
            color: #4f46e5; /* Indigo for hover */
        }
        /* Card Hover Effect */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        /* Scroll-Triggered Animation */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* Custom Dropdown Transition */
        .dropdown-transition {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        /* Header Gradient */
        header {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.95), rgba(209, 213, 219, 0.95));
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        /* Custom Section Tag Colors */
        .section-cics { background: #e5e7eb; color: #1f2937; }
        .section-cted { background: #d1d5db; color: #1f2937; }
        .section-ccje { background: #d4d4d8; color: #1f2937; }
        .section-chm { background: #d1d5db; color: #1f2937; }
        .section-cbea { background: #e5e7eb; color: #1f2937; }
        .section-ca { background: #d4d4d8; color: #1f2937; }
        .section-default { background: #e5e7eb; color: #1f2937; }
        /* Filter Button Styling */
        .filter-button {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .filter-button.active {
            background-color: #4f46e5; /* Indigo for active filter */
            color: #ffffff;
        }
        /* Minimalist Button Styling */
        .minimal-button {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .minimal-button:hover {
            background-color: #4f46e5; /* Indigo on hover */
            color: #ffffff;
        }

        
    </style>
</head>
<body x-data="{ selectedFilter: 'all' }">
    <header class="fixed top-0 left-0 w-full z-50" x-data="{ dropdownOpen: false, mobileMenuOpen: false }">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="/images/library.png" alt="CSU Library Logo" class="h-10 w-10 rounded-full">
                                <a href="{{ route('user.dashboard') }}" class="text-xl font-bold text-gray-900">Library</a>

            </div>
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('user.attendance.index') }}" class="text-gray-700 hover:text-indigo-600 transition hover-scale">Attendance</a>
                <a href="{{ route('user.books.index') }}" class="text-gray-700 hover:text-indigo-600 transition hover-scale">Books</a>
                <a href="#services" class="text-gray-700 hover:text-indigo-600 transition hover-scale">Services</a>
            </div>
            <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
                <button 
                    @click="dropdownOpen = !dropdownOpen" 
                    class="flex items-center focus:outline-none" 
                    :aria-expanded="dropdownOpen"
                    :aria-label="dropdownOpen ? 'Close profile menu' : 'Open profile menu'"
                >
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="w-10 h-10 rounded-full object-cover border-2 border-indigo-200" />
                    @else
                        <svg class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 p-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    @endif
                    <svg class="ml-2 w-4 h-4 text-gray-600 chevron-icon" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div 
                    x-show="dropdownOpen" 
                    x-transition:enter="dropdown-transition" 
                    x-transition:enter-start="opacity-0 transform scale-95" 
                    x-transition:enter-end="opacity-100 transform scale-100" 
                    x-transition:leave="dropdown-transition" 
                    x-transition:leave-start="opacity-100 transform scale-100" 
                    x-transition:leave-end="opacity-0 transform scale-95" 
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-100"
                >
                    <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Logout</button>
                    </form>
                </div>
            </div>
            <button 
                class="md:hidden" 
                @click="mobileMenuOpen = !mobileMenuOpen" 
                :aria-label="mobileMenuOpen ? 'Close menu' : 'Open menu'"
            >
                <svg class="w-6 h-6 text-gray-800" :class="{ 'hidden': mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="w-6 h-6 text-gray-800" :class="{ 'hidden': !mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </nav>
        <div 
            x-show="mobileMenuOpen" 
            x-transition:enter="dropdown-transition" 
            x-transition:enter-start="opacity-0 transform translate-y-2" 
            x-transition:enter-end="opacity-100 transform translate-y-0" 
            x-transition:leave="dropdown-transition" 
            x-transition:leave-start="opacity-100 transform translate-y-0" 
            x-transition:leave-end="opacity-0 transform translate-y-2" 
            class="md:hidden bg-white/95 backdrop-blur-md text-gray-800 px-4 py-4 border-t border-gray-100"
        >
            <a href="#about" class="block py-2 hover:text-indigo-600 transition hover-scale">About</a>
            <a href="#search" class="block py-2 hover:text-indigo-600 transition hover-scale">Search Catalog</a>
            <a href="#services" class="block py-2 hover:text-indigo-600 transition hover-scale">Services</a>
        </div>
    </header>
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 mt-24 fade-in">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">Available Books</h1>
            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2 scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
                <button 
                    @click="selectedFilter = 'all'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'all' }"
                >All</button>
                <button 
                    @click="selectedFilter = 'CICS'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CICS' }"
                >CICS</button>
                <button 
                    @click="selectedFilter = 'CTED'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CTED' }"
                >CTED</button>
                <button 
                    @click="selectedFilter = 'CCJE'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CCJE' }"
                >CCJE</button>
                <button 
                    @click="selectedFilter = 'CHM'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CHM' }"
                >CHM</button>
                <button 
                    @click="selectedFilter = 'CBEA'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CBEA' }"
                >CBEA</button>
                <button 
                    @click="selectedFilter = 'CA'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg filter-button minimal-button"
                    :class="{ 'active': selectedFilter === 'CA' }"
                >CA</button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <div 
                    class="bg-white rounded-xl shadow-sm card-hover scroll-reveal" 
                    x-data="{ visible: false }" 
                    x-intersect="visible = true" 
                    :class="{ 'visible': visible }" 
                    x-show="selectedFilter === 'all' || selectedFilter === '{{ $book->section }}'"
                >
                    <div class="relative h-48 bg-gray-100">
                        @if($book->image1)
                            <img src="{{ asset('storage/' . $book->image1) }}" alt="{{ $book->name }}" class="w-full h-full object-cover rounded-t-xl" />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500 rounded-t-xl">
                                No Image
                            </div>
                        @endif
                    <span class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full
                        @if($book->section === 'CICS') bg-orange-200 text-gray-800
                        @elseif($book->section === 'CTED') bg-sky-200 text-gray-800
                        @elseif($book->section === 'CCJE') bg-red-300 text-gray-800
                        @elseif($book->section === 'CHM') bg-pink-300 text-gray-800
                        @elseif($book->section === 'CBEA') bg-yellow-200 text-gray-800
                        @elseif($book->section === 'CA') bg-green-300 text-gray-800
                        @else bg-gray-100 text-gray-800 @endif">
                            {{ $book->section }}
                    </span>
                    @if($book->isBorrowed())
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">Borrowed
                            @if($book->borrowedBy())
                                by {{ $book->borrowedBy()->lname ?? '' }}
                            @endif
                        </span>
                    @endif
                    </div>
                    <div class="p-5">
                        <p class="text-md text-gray-800 mb-2">{{ $book->book_code }}</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $book->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">by {{ $book->author }}</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('user.books.show', $book->id) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg minimal-button">View</a>
                            <form action="{{ route('user.books.reserve', $book->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reserve this book?');">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        // Initialize Intersection Observer for scroll-reveal animations
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.scroll-reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>