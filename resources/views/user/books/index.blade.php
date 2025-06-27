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
    <script src="https://unpkg.com/heroicons@2.0.16/dist/24/outline/search.mjs" type="module"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom Gradient Background */
        body {
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

    <x-header />

    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 mt-24 fade-in">
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-full shadow bg-gray-100 p-1">
                <button id="library-books-btn" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white text-gray-700 bg-white shadow active" style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);">Library Book</button>
                <button id="ebook-btn" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white text-gray-500 bg-transparent">E-Book</button>
            </div>
        </div>
        <div id="library-books-container">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                <h1 class="text-3xl font-bold text-gray-800 scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">Available Books</h1>
                <div class="flex flex-col md:flex-row md:items-center gap-4 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <input id="library-search-input" type="text" placeholder="Search books..." class="w-full pr-10 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition-all" />
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></svg>
                        </span>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @keydown.escape="open = false" type="button" class="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-indigo-100 transition-colors min-w-[120px]">
                            <span x-text="selectedFilter.charAt(0).toUpperCase() + selectedFilter.slice(1)"></span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.away="open = false" class="absolute z-10 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                            <template x-for="section in ['all', 'CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA']" :key="section">
                                <button @click="selectedFilter = section; open = false" type="button" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-100 transition-colors" :class="{ 'bg-indigo-500 text-white': selectedFilter === section }">
                                    <span x-text="section.charAt(0).toUpperCase() + section.slice(1)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
                    {{ session('success') }}
                </div>
            @endif
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
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
        <div id="ebook-container" style="display: none;">
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="AI">AI</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Agriculture">Agriculture</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Fiction">Fiction</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Science">Science</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="History">History</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Biography">Biography</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Technology">Technology</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Art">Art</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Business">Business</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Health">Health</button>

            </div>
            <div class="row mb-3">
                <div class="col-md-8 mx-auto">
                    <form id="ebook-search-form" class="relative w-full md:w-1/2">
                        <input 
                            type="text" 
                            id="ebook-search-input" 
                            class="w-full p-3 pl-4 pr-12 rounded-lg border border-gray-300 bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 placeholder-gray-500" 
                            placeholder="Search for e-books..." 
                            value=""
                            required
                        >
                        <button 
                            type="submit" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 transition-colors duration-300"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <div id="ebook-results" class="flex flex-wrap justify-center">
                <!-- E-Book cards will be rendered here -->
            </div>
        </div>
    </div>

    <x-footer />
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