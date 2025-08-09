<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    <!-- Include Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-figtree bg-gray-50 text-gray-900">

<x-header />

    <div id="home" class="main mt-20">

        <main id="home" class="mt-1">
            <section class="big-hero" style="font-size: 65px;">The Future of <br> Cagayan State University</section>
            <section class="small-hero">Discover our new technology! It’s simple, smart, and designed to help you learn better. </section>
        </main>

        <div class="container">
            <div class="item item-1"><img src="images/library-images/books2.jpg" alt=""></div>
            <div class="item item-2"><img src="images/library-images/books1.jpg" alt=""></div>
            <div class="item item-3">
                <div class="hero-btn">
                    <a href="" class="explore">Exploree</a>
                </div>
                <div>
                    <img src="images/library-images/books4.jpg" alt="">
                </div>
            </div>
            <div class="item item-4"><img src="images/library-images/books5.jpg" style="aspect-ratio: 1/4;" alt=""></div>
            <div class="item item-5"><img src="images/library-images/books3.jpg" alt=""></div>
        </div>
        

    </div>
    
        <div class="flex justify-center mb-5">
        <section class="relative bg-gradient-to-br from-indigo-900  to-gray-900 py-12 sm:py-16 px-4 sm:px-6 lg:px-8 overflow-hidden mt-12 rounded-[100px] w-[90%]">   
            <div class="absolute inset-0 bg-[url('/images/book-pattern.png')] opacity-10 bg-repeat"></div>
            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-gray-900 to-transparent"></div>
            <div class="relative max-w-7xl mx-auto text-center">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6 animate-on-scroll" 
                    x-data="{ visible: false, initialized: false }" 
                    x-init="setTimeout(() => initialized = true, 100)" 
                    x-intersect="visible = true" 
                    :class="{ 'opacity-0 translate-y-10': !visible && initialized, 'opacity-100 translate-y-0': visible || !initialized }" 
                    x-transition:enter="transition duration-700 ease-out" 
                    x-transition:enter-start="opacity-0 translate-y-10" 
                    x-transition:enter-end="opacity-100 translate-y-0">
                    Ready to Explore the World of Books?
                </h1>
                <p class="text-base sm:text-lg lg:text-xl text-gray-300 max-w-3xl mx-auto mb-6 sm:mb-8 animate-on-scroll" 
                x-data="{ visible: false, initialized: false }" 
                x-init="setTimeout(() => initialized = true, 200)" 
                x-intersect="visible = true" 
                :class="{ 'opacity-0 translate-y-10': !visible && initialized, 'opacity-100 translate-y-0': visible || !initialized }" 
                x-transition:enter="transition duration-700 ease-out delay-200" 
                x-transition:enter-start="opacity-0 translate-y-10" 
                x-transition:enter-end="opacity-100 translate-y-0">
                    Take the first step today. Find your ideal book, build knowledge, and join thousands of avid readers.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-on-scroll" 
                    x-data="{ visible: false, initialized: false }" 
                    x-init="setTimeout(() => initialized = true, 300)" 
                    x-intersect="visible = true" 
                    :class="{ 'opacity-0 translate-y-10': !visible && initialized, 'opacity-100 translate-y-0': visible || !initialized }" 
                    x-transition:enter="transition duration-700 ease-out delay-400" 
                    x-transition:enter-start="opacity-0 translate-y-10" 
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <a href="{{ route('user.books.index') }}" 
                    class="inline-flex items-center px-6 py-3 bg-white text-indigo-900 text-sm sm:text-base font-medium rounded-lg shadow-md hover:bg-gray-100 hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-book mr-2"></i>
                        Find Your Book
                    </a>
                    <a href="{{ route('user.dashboard') }}" 
                    class="inline-flex items-center px-6 py-3 bg-indigo-700 text-white text-sm sm:text-base font-medium rounded-lg shadow-md hover:bg-indigo-800 hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                </div>
                <div class="mt-6 text-sm text-gray-400 flex justify-center gap-8 animate-on-scroll" 
                    x-data="{ visible: false, initialized: false }" 
                    x-init="setTimeout(() => initialized = true, 400)" 
                    x-intersect="visible = true" 
                    :class="{ 'opacity-0 translate-y-10': !visible && initialized, 'opacity-100 translate-y-0': visible || !initialized }" 
                    x-transition:enter="transition duration-700 ease-out delay-600" 
                    x-transition:enter-start="opacity-0 translate-y-10" 
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <span><i class="fas fa-users mr-1"></i> 5,000+ Community Members</span>
                    <span><i class="fas fa-book-open mr-1"></i> 50+ Book Categories</span>
                    <span><i class="fas fa-check-circle mr-1"></i> 98% Satisfaction</span>
                </div>
            </div>
        </section>
    </div>

<x-footer />

<!-- JavaScript for Mobile Menu and Scroll Animations -->
<script>
    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

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