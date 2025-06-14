<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32?text=CSU">
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/user/dashboard-responsive.css') }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'csu-blue': '#1e3a8a', 
                        'csu-light-blue': '#3b82f6',
                        'csu-accent': '#f59e0b', 
                    },
                    fontFamily: {
                        figtree: ['Figtree', 'sans-serif'],
                    },
                },
            },
        };
    </script>

</head>
<body class="font-figtree bg-gray-50 text-gray-900">

<header class="bg-white/10 backdrop-blur-md animate-on-load fixed top-0 left-0 w-full z-50">

    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <!-- Placeholder Logo (replace with CSU logo) -->
            <img src="images/library.png" alt="CSU Library Logo" class="h-10 w-10">
            <a href="user.dashboard" class="text-xl font-bold text-gray-900">CSU Library</a>
        </div>
        <div class="hidden md:flex space-x-6">
            <a href="{{ route('user.attendance.index') }}" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">Attendance</a>
            <a href="{{route('user.books.index')}}" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">Books</a>
            <a href="#services" class="text-gray-900 hover:text-csu-light-blue transition hover:scale-105 transform duration-300">Services</a>
        </div>
        <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
            <button 
                @click="dropdownOpen = !dropdownOpen" 
                class="flex items-center focus:outline-none" 
                :aria-expanded="dropdownOpen"
                :aria-label="dropdownOpen ? 'Close profile menu' : 'Open profile menu'"
            >
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="w-10 h-10 rounded-full object-cover" />
                @else
                    <svg class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 p-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                @endif
                <svg class="ml-2 w-4 h-4 text-gray-600 chevron-icon" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
        <button class="md:hidden" id="menu-toggle" aria-label="Open menu">
            <svg class="w-6 h-6 text-gray-900 hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg class="w-6 h-6 text-gray-900 x-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </nav>
    <!-- Mobile Menu (Hidden by Default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white/10 backdrop-blur-md text-gray-900 px-4 py-4">
        <a href="#about" class="block py-2 hover:text-csu-light-blue transition">About</a>
        <a href="#search" class="block py-2 hover:text-csu-light-blue transition">Search Catalog</a>
        <a href="#services" class="block py-2 hover:text-csu-light-blue transition">Services</a>
    </div>
    <!-- Scripts -->
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Mobile Menu Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                menuToggle.classList.toggle('is-open');
                menuToggle.setAttribute('aria-label', menuToggle.classList.contains('is-open') ? 'Close menu' : 'Open menu');
            });
        });
    </script>
</header>


    <main id="home" class="mt-14">
        <section class="big-hero">The Future of <br> Cagayan State University</section>
        <section class="small-hero">Discover our new technology! It’s simple, smart, and designed to help you learn better. </section>
    </main>

    <div class="container">
        <div class="item item-1"><img src="images/library-images/books2.jpg" alt=""></div>
        <div class="item item-2"><img src="images/library-images/books1.jpg" alt=""></div>
        <div class="item item-3">
            <div class="hero-btn">
                <a href="" class="explore">Exploree</a>
                <a href="" class="know">View pages</a>
            </div>
            <div>
                <img src="images/library-images/books4.jpg" alt="">
            </div>
        </div>
        <div class="item item-4"><img src="images/library-images/books5.jpg" style="aspect-ratio: 1/4;" alt=""></div>
        <div class="item item-5"><img src="images/library-images/books3.jpg" alt=""></div>
    </div>

    <!-- About the Library -->
    <section id="about" class="py-24 bg-white animate-on-scroll slideInLeft">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-csu-blue mb-8 text-center">About CSU Library</h2>
            <p class="text-lg text-gray-600 mb-12 text-center max-w-4xl mx-auto">
                The CSU Library is the heart of academic excellence, offering a vast collection of books, journals, and digital resources to support students, faculty, and researchers.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <svg class="w-10 h-10 text-csu-blue mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="text-xl font-semibold text-csu-blue mb-3 text-center">Extensive Collection</h3>
                    <p class="text-gray-600 text-center">Access thousands of books, journals, and more.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <svg class="w-10 h-10 text-csu-blue mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-csu-blue mb-3 text-center">Research Support</h3>
                    <p class="text-gray-600 text-center">Expert help for your academic projects.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <svg class="w-10 h-10 text-csu-blue mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                    <h3 class="text-xl font-semibold text-csu-blue mb-3 text-center">Modern Facilities</h3>
                    <p class="text-gray-600 text-center">Study spaces, computers, and Wi-Fi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Catalog -->
    <section id="search" class="py-24 bg-gray-100 animate-on-scroll fadeInUp">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-csu-blue mb-8 text-center">Search Our Catalog</h2>
            <p class="text-lg text-gray-600 mb-12 text-center max-w-4xl mx-auto">
                Find books, articles, and more in our extensive library collection.
            </p>
            <div class="max-w-3xl mx-auto">
                <form action="#" class="flex items-center bg-white rounded-lg shadow-md">
                    <input type="text" placeholder="Search for books, journals, or authors..." class="flex-1 p-4 text-gray-900 bg-transparent outline-none" aria-label="Search catalog">
                    <button type="submit" class="p-4 text-white bg-csu-blue hover:bg-csu-light-blue rounded-r-lg transition hover:scale-105 transform duration-300" aria-label="Search">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </section>


    <!-- Opening Hours -->
    <section id="hours" class="py-24 bg-gray-100 animate-on-scroll fadeInUp">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-csu-blue mb-8 text-center">Opening Hours</h2>
            <p class="text-lg text-gray-600 mb-12 text-center max-w-4xl mx-auto">
                Visit us during our open hours to explore our resources and services.
            </p>
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
                <ul class="space-y-4 text-gray-600">
                    <li class="flex justify-between">
                        <span class="font-medium">Monday - Friday</span>
                        <span>8:00 AM - 8:00 PM</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="font-medium">Saturday</span>
                        <span>Closed</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="font-medium">Sunday</span>
                        <span>Closed</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Library Services -->
    <section id="services" class="py-24 bg-white animate-on-scroll slideInLeft">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-csu-blue mb-8 text-center">Library Services</h2>
            <p class="text-lg text-gray-600 mb-12 text-center max-w-4xl mx-auto">
                Discover the range of services we offer to support your academic journey.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12">
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <h3 class="text-xl font-semibold text-csu-blue mb-3">Borrowing</h3>
                    <p class="text-gray-600">Check out books and other materials with ease.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <h3 class="text-xl font-semibold text-csu-blue mb-3">Research Help</h3>
                    <p class="text-gray-600">Get personalized assistance from our librarians.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition transform hover:scale-105 duration-300">
                    <h3 class="text-xl font-semibold text-csu-blue mb-3">QR Code Attendance</h3>
                    <p class="text-gray-600">Faster logging of attendance and different variety of activities.</p>
                </div>
            </div>
        </div>
    </section>

  
    <!-- Footer -->
    <footer class="bg-csu-blue text-white py-16 animate-on-load">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <p class="text-gray-200">CSU Library<br>123 University Ave, City, State 12345<br>Email: <a href="mailto:library@csu.edu" class="hover:text-csu-light-blue transition">library@csu.edu</a><br>Phone: (123) 456-7890</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{route('user.attendance.index')}}" class="hover:text-csu-light-blue transition">Attendance</a></li>
                        <li><a href="#" class="hover:text-csu-light-blue transition">Library Policies</a></li>
                        <li><a href="#" class="hover:text-csu-light-blue transition">Accessibility</a></li>
                        <li><a href="#hours" class="hover:text-csu-light-blue transition">Opening Hours</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Stay Connected</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-200 hover:text-csu-light-blue transition" aria-label="Facebook">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-200 hover:text-csu-light-blue transition" aria-label="Twitter">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-200 hover:text-csu-light-blue transition" aria-label="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.948-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-600 text-center">
                <p class="text-gray-200">© 2025 CSU Library. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Mobile Menu and Scroll Animations -->
    <script>
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Scroll Animation with Intersection Observer
        const animateElements = document.querySelectorAll('.animate-on-scroll');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animateElements.forEach(element => observer.observe(element));
    </script>
</body>
</html>