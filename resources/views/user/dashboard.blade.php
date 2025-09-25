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

    <!-- Campus News Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-semibold text-gray-900 mb-4">Campus News</h2>
                <p class="text-lg text-gray-600">Stay updated with the latest happenings at Cagayan State University</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($campusNews) && $campusNews->count() > 0)
                    @foreach($campusNews as $news)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            @if($news->featured_image)
                                <div class="aspect-w-16 aspect-h-9">
                                    <img src="{{ asset('storage/' . $news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-48 object-cover">
                                </div>
                            @endif
                            <div class="p-6">
                                <div class="flex items-center text-sm text-gray-500 mb-2">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    {{ $news->created_at->format('M d, Y') }}
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">{{ $news->title }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-3">{{ $news->excerpt }}</p>
                                <div class="mt-4">
                                    <a href="{{ route('campus-news.show', $news) }}" class="text-csu-blue hover:text-csu-light-blue font-medium text-sm transition-colors duration-200">
                                        Read more <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No campus news available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
    
    <x-books-component/>

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