<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Book Details - CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/favicon/library.png" />
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
        /* Image Zoom Animation */
        .image-zoom {
            transition: transform 0.3s ease;
        }
        .image-zoom:hover {
            transform: scale(1.1);
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
        /* Thumbnail Styling */
        .thumbnail {
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        .thumbnail.active {
            border-color: #4f46e5; /* Indigo border for active thumbnail */
        }
        /* Full Image Container */
        .full-image-container {
            max-height: 600px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .full-image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Ensure full image is visible without cropping */
        }
    </style>
</head>
<body x-data="{ selectedImage: '{{ !empty($book->image1) ? asset('storage/' . $book->image1) : '' }}' }">
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
    <main class="max-w-6xl mx-auto p-6 mt-24 fade-in">
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Book Information and Thumbnails (Left) -->
            <div class="scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ $book->name }}</h2>
                <p class="text-gray-600 mb-3"><strong>Author:</strong> {{ $book->author }}</p>
                @for ($i = 1; $i <= 5; $i++)
                    @php $authorNumber = 'author_number' . $i; @endphp
                    @if (!empty($book->$authorNumber))
                        <p class="text-gray-600 mb-3"><strong>Author Number {{ $i }}:</strong> {{ $book->$authorNumber }}</p>
                    @endif
                @endfor
                <p class="text-gray-600 mb-3"><strong>Description:</strong> {{ $book->description }}</p>
                <p class="text-gray-600 mb-3"><strong>Section:</strong> {{ $book->section }}</p>
                <!-- Thumbnail Pagination -->
                <div class="mt-6 flex flex-wrap gap-4">
                    @for ($i = 1; $i <= 5; $i++)
                        @php $imageField = 'image' . $i; @endphp
                        @if (!empty($book->$imageField))
                            <img 
                                src="{{ asset('storage/' . $book->$imageField) }}" 
                                alt="Book Thumbnail {{ $i }}" 
                                class="w-16 h-16 object-cover rounded-md border-2 thumbnail" 
                                :class="{ 'active': selectedImage === '{{ asset('storage/' . $book->$imageField) }}' }" 
                                @click="selectedImage = '{{ asset('storage/' . $book->$imageField) }}'" 
                            />
                        @endif
                    @endfor
                </div>
                <div class="mt-8">
                    <a href="{{ route('user.books.index') }}" class="inline-block px-6 py-3 border-2 border-blue-800 text-gray-800 rounded-lg hover:from-indigo-700 hover:to-violet-700 transition hover-scale">Back to Books</a>
                </div>
            </div>
            <!-- Full-Size Image (Right) -->
            <div class="full-image-container scroll-reveal" x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
                <img 
                    x-bind:src="selectedImage" 
                    alt="Selected Book Image" 
                    class="rounded-lg shadow-md" 
                    x-show="selectedImage"
                />
                <div x-show="!selectedImage" class="text-gray-500 text-center">Select an image to view</div>
            </div>
        </div>
    </main>
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