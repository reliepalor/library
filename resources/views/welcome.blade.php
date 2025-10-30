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

    <div id="home" class="mt-20">
        <main id="home" class="mt-8 sm:mt-6 md:mt-1 text-center opacity-0 animate-[slideIn_1s_ease-out_0.5s_forwards] flex flex-col items-center">
            <section class="text-[rgb(53,53,53)] text-[6vw] mb-[1%] lg:text-[6vw] md:text-[clamp(2rem,8vw,4rem)] md:leading-[1.1] md:mt-[0px]">
                The Future of <br> CSU-Gonzaga Library.
            </section>
            <section class="text-center w-[21rem] mx-auto leading-[25px] p-[5px] md:w-full md:max-w-[90vw] md:p-[10px] md:text-[clamp(0.9rem,4vw,1.2rem)]">
                Discover our new technology! It's simple, smart, and designed to help you learn better.
            </section>
        </main>

        <!--IMAGES-->
        <div class="flex justify-center ">
            <!-- Desktop Layout -->
            <div class="hidden w-full md:flex justify-center items-center flex-wrap gap-[1%] mb-[5%] relative desktop-layout">
                <!-- Item 1 -->
                <div class="w-[10%] opacity-0 animate-[fadeInUp_1s_ease-out_1s_forwards] relative h-[22rem] min-w-[18rem]">
                    <img src="images/library-images/books1.jpg" 
                        alt="Stack of colorful books on a shelf"
                        class="rounded-[30px] w-full aspect-[2/3] object-cover transition-all duration-400 ease-in-out block hover:scale-105 hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)] h-[22rem]">
                </div>

                <!-- Item 2 -->
                <div class="w-[10%] opacity-0 animate-[fadeInUp_1s_ease-out_1s_forwards] relative mt-[3.3%] h-[18rem] min-w-[12rem]">
                    <img src="images/library-images/book1.jpg" 
                        alt="Open book with pages turning"
                        class="rounded-[30px] w-full aspect-[2/3] object-cover transition-all duration-400 ease-in-out block hover:scale-105 hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)] h-[18rem]">
                </div>

                <!-- Item 3 -->
                <div class="w-[10%] opacity-0 animate-[fadeInUp_1s_ease-out_1s_forwards] relative mt-[2.7%] h-[18rem] min-w-[13rem]">
                    <div class="absolute top-[10px] left-1/2 -translate-x-1/2 z-[2] flex flex-col gap-[5px] h-auto justify-center items-center">
                        <a href="#" class="text-center w-[100px] bg-black text-white rounded-[20px] py-[10px] px-[10px] no-underline inline-block transition-all duration-300 ease-in-out text-[0.9rem] hover:bg-[rgb(53,53,53)] hover:-translate-y-[3px] hover:scale-[1.02] focus:bg-[rgb(53,53,53)] focus:-translate-y-[3px] focus:scale-[1.02]">
                            Explore
                        </a>
                    </div>
                    <div>
                        <img src="images/library-images/book4.jpg" 
                            alt="Cozy reading nook with books" 
                            class="shadow-md border border-gray-200 mt-[140px] flex justify-end items-end rounded-[30px] h-[10rem] w-full">
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="w-[10%] opacity-0 animate-[fadeInUp_1s_ease-out_1s_forwards] relative mt-[3.3%] h-[18rem] min-w-[12rem]">
                    <img src="images/library-images/book5.jpg" 
                        alt="Vintage library interior"
                        class="rounded-[30px] w-full aspect-[2/3] object-cover transition-all duration-400 ease-in-out block hover:scale-105 hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)] h-[18rem]"
                        style="aspect-ratio: 1/4;">
                </div>

                <!-- Item 5 -->
                <div class="w-[10%] opacity-0 animate-[fadeInUp_1s_ease-out_1s_forwards] relative h-[22rem] min-w-[18rem]">
                    <img src="images/library-images/books5.jpg" 
                        alt="Books arranged in a artistic pattern"
                        class="rounded-[30px] w-full aspect-[2/3] object-cover transition-all duration-400 ease-in-out block hover:scale-105 hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)] h-[22rem]">
                </div>
            </div>

            <!-- Mobile/Tablet Carousel -->
            <div class="md:hidden w-full max-w-7xl px-4 mobile-carousel">
                <div class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth [-webkit-overflow-scrolling:touch] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" 
                    id="carousel">
                    <!-- Carousel Item 1 -->
                    <div class="w-full flex-shrink-0 snap-start">
                        <div class="px-2">
                            <img src="images/library-images/books1.jpg" 
                                alt="Stack of colorful books on a shelf" 
                                class="w-full h-96 object-cover rounded-lg">
                        </div>
                    </div>

                    <!-- Carousel Item 2 -->
                    <div class="w-full flex-shrink-0 snap-start">
                        <div class="px-2">
                            <img src="images/library-images/book1.jpg" 
                                alt="Open book with pages turning" 
                                class="w-full h-96 object-cover rounded-lg">
                        </div>
                    </div>

                    <!-- Carousel Item 3 -->
                    <div class="w-full flex-shrink-0 snap-start">
                        <div class="px-2 flex flex-col justify-center items-center h-96 bg-white rounded-lg">
                            <div class="mb-8">
                                <a href="#" class="text-center w-[80px] bg-black text-white rounded-[20px] py-[10px] px-[10px] no-underline inline-block transition-all duration-300 ease-in-out text-base mt-[70px] hover:bg-[rgb(53,53,53)] hover:-translate-y-[3px] hover:scale-[1.02]">
                                    Explore
                                </a>
                            </div>
                            <img src="images/library-images/book4.jpg" 
                                alt="Cozy reading nook with books" 
                                class="shadow-md border border-gray-200 rounded max-w-xs">
                        </div>
                    </div>

                    <!-- Carousel Item 4 -->
                    <div class="w-full flex-shrink-0 snap-start">
                        <div class="px-2">
                            <img src="images/library-images/book5.jpg" 
                                alt="Vintage library interior" 
                                class="w-full h-96 object-cover rounded-lg">
                        </div>
                    </div>

                    <!-- Carousel Item 5 -->
                    <div class="w-full flex-shrink-0 snap-start">
                        <div class="px-2">
                            <img src="images/library-images/books5.jpg" 
                                alt="Books arranged in a artistic pattern" 
                                class="w-full h-96 object-cover rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Carousel Dots Indicator -->
                <div class="flex justify-center gap-2 mt-4">
                    <span class="dot w-2 h-2 rounded-full bg-gray-300 cursor-pointer transition-all duration-300 active:bg-blue-500 active:w-6 active:rounded" data-slide="0"></span>
                    <span class="dot w-2 h-2 rounded-full bg-gray-300 cursor-pointer transition-all duration-300" data-slide="1"></span>
                    <span class="dot w-2 h-2 rounded-full bg-gray-300 cursor-pointer transition-all duration-300" data-slide="2"></span>
                    <span class="dot w-2 h-2 rounded-full bg-gray-300 cursor-pointer transition-all duration-300" data-slide="3"></span>
                    <span class="dot w-2 h-2 rounded-full bg-gray-300 cursor-pointer transition-all duration-300" data-slide="4"></span>
                </div>
            </div>
        </div>
    </div>

    <x-books-component/>

    <x-library-policy />

<!-- Campus News Section -->
<section class="py-8 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 md:mb-12">
            <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-2 md:mb-4">Campus News</h2>
            <p class="text-base md:text-lg text-gray-600">Stay updated with the latest happenings at CSU Gonzaga Campus</p>
        </div>
        
        <!-- Desktop & Tablet Layout (md and above) -->
        <div class="hidden md:flex gap-8 h-[600px] lg:h-[700px]">
            <!-- Left Side - Featured News (70%) -->
            <div class="w-[70%]">
                <div id="featured-news" class="bg-white rounded-2xl border border-gray-200/60 overflow-hidden h-full transition-all duration-500 ease-out shadow-sm hover:shadow-lg flex flex-col">
                    @if(isset($campusNews) && count($campusNews) > 0)
                        <div class="featured-content opacity-100 transition-opacity duration-300 flex flex-col h-full">
                            @if($campusNews[0]->featured_image)
                                <div class="flex-shrink-0 bg-gray-100 h-64 lg:h-80">
                                    <img src="{{ asset('storage/' . $campusNews[0]->featured_image) }}" 
                                        alt="{{ $campusNews[0]->title }}" 
                                        class="w-full h-full object-contain transition-transform duration-700 ease-out rounded-xl">
                                </div>
                            @endif
                            <div class="p-4 lg:p-6 flex-1 flex flex-col">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                    <span class="font-medium">{{ $campusNews[0]->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($campusNews[0]->category ?? 'Announcement') }}
                                    </span>
                                    @if($campusNews[0]->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3 leading-tight">{{ $campusNews[0]->title }}</h3>
                                    <p class="text-gray-600 leading-relaxed mb-4 flex-1">{{ $campusNews[0]->excerpt }}</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('campus-news.show', $campusNews[0]) }}" 
                                    class="bg-white inline-flex items-center text-csu-blue hover:text-csu-light-blue font-medium transition-all duration-200 hover:translate-x-1">
                                        Read full article
                                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No campus news available at the moment.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Side - News List (30%) -->
            <div class="w-[30%]">
                <div class="bg-white rounded-2xl border border-gray-200/60 h-full overflow-hidden shadow-sm">
                    <div class="p-4 lg:p-6 border-b border-gray-100/80">
                        <h4 class="font-semibold text-gray-900">Latest News</h4>
                        <p class="text-sm text-gray-500 mt-1">Click to read</p>
                    </div>
                    <div class="overflow-y-auto h-[calc(100%-80px)] custom-scrollbar">
                        @forelse($campusNews ?? [] as $index => $news)
                            <div class="news-item border-b border-gray-50 hover:bg-gray-50/50 transition-all duration-200 cursor-pointer {{ $index === 0 ? 'bg-blue-50/30 border-l-2 border-l-csu-blue' : '' }}"
                                onclick="showFeaturedNews({{ $index }}, this)"
                                data-news-index="{{ $index }}"
                                data-title="{{ $news->title }}"
                                data-excerpt="{{ $news->excerpt }}"
                                data-date="{{ $news->created_at->format('M d, Y') }}"
                                data-image="{{ $news->featured_image ? asset('storage/' . $news->featured_image) : '' }}"
                                data-url="{{ route('campus-news.show', $news) }}"
                                data-category="{{ $news->category }}"
                                data-is_featured="{{ $news->is_featured ? 'true' : 'false' }}">
                                <div class="p-3 lg:p-4">
                                    <div class="flex gap-3">
                                        @if($news->featured_image)
                                            <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $news->featured_image) }}" 
                                                    alt="{{ $news->title }}" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                                <i class="fas fa-newspaper text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h5 class="font-medium text-gray-900 text-xs lg:text-sm line-clamp-2 mb-1">{{ $news->title }}</h5>
                                            <p class="text-xs text-gray-500 mb-1 lg:mb-2">{{ $news->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-600 line-clamp-2 hidden lg:block">{{ $news->excerpt }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center">
                                <i class="fas fa-newspaper text-2xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500 text-sm">No news available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Layout - Apple-Inspired Card Carousel -->
        <div class="md:hidden">
            <!-- Featured Article with Blur Background -->
            @if(isset($campusNews) && count($campusNews) > 0)
                <div class="relative mb-6 overflow-hidden rounded-3xl">
                    <!-- Blurred Background Image -->
                    @if($campusNews[0]->featured_image)
                        <div class="absolute inset-0 opacity-30 blur-2xl scale-110">
                            <img src="{{ asset('storage/' . $campusNews[0]->featured_image) }}" 
                                alt="" 
                                class="w-full h-full object-cover">
                        </div>
                    @endif
                    
                    <!-- Main Card -->
                    <div class="relative bg-white/95 backdrop-blur-xl rounded-3xl overflow-hidden shadow-2xl border border-white/20 transform transition-all duration-500 hover:scale-[1.02]">
                        @if($campusNews[0]->featured_image)
                            <div class="relative overflow-hidden">
                                <img src="{{ asset('storage/' . $campusNews[0]->featured_image) }}" 
                                    alt="{{ $campusNews[0]->title }}" 
                                    class="w-full h-56 object-cover transform transition-transform duration-700 hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                
                                <!-- Floating Badges -->
                                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-white/90 backdrop-blur-md text-gray-900 shadow-lg">
                                        {{ ucfirst($campusNews[0]->category ?? 'Announcement') }}
                                    </span>
                                    @if($campusNews[0]->is_featured)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-400/90 backdrop-blur-md text-yellow-900 shadow-lg">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-5">
                            <div class="flex items-center text-xs text-gray-500 mb-3">
                                <div class="flex items-center bg-gray-100 rounded-full px-3 py-1">
                                    <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i>
                                    <span class="font-medium">{{ $campusNews[0]->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight tracking-tight">{{ $campusNews[0]->title }}</h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">{{ $campusNews[0]->excerpt }}</p>
                            
                            <a href="{{ route('campus-news.show', $campusNews[0]) }}" 
                                class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5 active:scale-95">
                                <span>Read Full Story</span>
                                <i class="fas fa-arrow-right ml-2 text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Horizontal Scrolling News Cards -->
            @if(isset($campusNews) && count($campusNews) > 1)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h4 class="text-lg font-bold text-gray-900">More Stories</h4>
                        <div class="flex gap-2">
                            <button onclick="scrollNewsLeft()" class="w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-all active:scale-90">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                            <button onclick="scrollNewsRight()" class="w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-all active:scale-90">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="news-scroll-container" class="flex gap-4 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-4 -mx-4 px-4 scroll-smooth">
                        @foreach(array_slice($campusNews->toArray(), 1) as $news)
                            <div class="flex-shrink-0 w-[280px] snap-start group">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 h-full transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                                    @if($news['featured_image'])
                                        <div class="relative overflow-hidden h-40">
                                            <img src="{{ asset('storage/' . $news['featured_image']) }}" 
                                                alt="{{ $news['title'] }}" 
                                                class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                    @else
                                        <div class="h-40 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <i class="fas fa-newspaper text-3xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <div class="flex items-center text-xs text-gray-500 mb-2">
                                            <i class="far fa-clock mr-1.5 text-gray-400"></i>
                                            <span>{{ \Carbon\Carbon::parse($news['created_at'])->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <h5 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2 leading-snug min-h-[2.5rem]">{{ $news['title'] }}</h5>
                                        <p class="text-gray-600 text-xs line-clamp-2 mb-3 leading-relaxed">{{ $news['excerpt'] }}</p>
                                        
                                        <a href="{{ route('campus-news.show', $news['id']) }}" 
                                            class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold text-xs transition-all duration-200 group-hover:translate-x-1">
                                            <span>Read More</span>
                                            <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Scroll Progress Indicator -->
                    <div class="flex justify-center gap-1.5 mt-4">
                        @foreach(array_slice($campusNews->toArray(), 1) as $index => $news)
                            <div class="h-1 w-8 rounded-full bg-gray-200 overflow-hidden">
                                <div class="scroll-indicator h-full bg-blue-600 rounded-full w-0 transition-all duration-300"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>


 <x-library-policy />
@stack('scripts')

<script>
    //-------------------------------------------------------------------------------CHERO Section
   const carousel = document.getElementById('carousel');
    const dots = document.querySelectorAll('.dot');
    
    // Update active dot based on scroll position
    carousel.addEventListener('scroll', () => {
        const scrollPosition = carousel.scrollLeft;
        const itemWidth = carousel.offsetWidth;
        const currentIndex = Math.round(scrollPosition / itemWidth);
        
        dots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    });
    
    // Click on dots to navigate
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            const itemWidth = carousel.offsetWidth;
            carousel.scrollTo({
                left: itemWidth * index,
                behavior: 'smooth'
            });
        });
    });
  //-------------------------------------------------------------------------------CAMPUS NEWSS
  // Desktop/Tablet Featured News Function
function showFeaturedNews(index, clickedElement) {
    // Only run on desktop/tablet view
    if (window.innerWidth < 768) return;

    const featuredNews = document.getElementById('featured-news');
    const featuredContent = featuredNews.querySelector('.featured-content');

    // Get data from clicked element
    const title = clickedElement.dataset.title;
    const excerpt = clickedElement.dataset.excerpt;
    const date = clickedElement.dataset.date;
    const image = clickedElement.dataset.image;
    const url = clickedElement.dataset.url;
    const category = clickedElement.dataset.category;
    const isFeatured = clickedElement.dataset.is_featured === 'true';

    // Remove active state from all news items
    document.querySelectorAll('.news-item').forEach(item => {
        item.classList.remove('bg-blue-50/30', 'border-l-2', 'border-l-csu-blue');
    });

    // Add active state to clicked item
    clickedElement.classList.add('bg-blue-50/30', 'border-l-2', 'border-l-csu-blue');

    // Fade out current content
    featuredContent.style.opacity = '0';
    featuredContent.style.transform = 'translateY(10px)';

    setTimeout(() => {
        // Determine image height based on screen size
        const isLargeScreen = window.innerWidth >= 1024;
        const imageHeight = isLargeScreen ? 'h-80' : 'h-64';

        // Update content
        const imageHtml = image ? 
            `<div class="flex-shrink-0 bg-gray-100 ${imageHeight}">
                <img src="${image}" alt="${title}" class="w-full h-full object-contain transition-transform duration-700 ease-out">
             </div>` : '';

        const categoryBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${category.charAt(0).toUpperCase() + category.slice(1)}</span>`;
        const featuredBadge = isFeatured ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-star mr-1"></i>Featured</span>` : '';
        const badgesHtml = `<div class="flex flex-wrap gap-2 mb-3">${categoryBadge}${isFeatured ? ' ' + featuredBadge : ''}</div>`;

        const padding = isLargeScreen ? 'p-6' : 'p-4';
        const titleSize = isLargeScreen ? 'text-xl' : 'text-lg';

        featuredContent.innerHTML = `
            ${imageHtml}
            <div class="${padding} flex-1 flex flex-col">
                <div class="flex items-center text-sm text-gray-500 mb-3">
                    <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                    <span class="font-medium">${date}</span>
                </div>
                ${badgesHtml}
                <div class="flex-1">
                    <h3 class="${titleSize} font-semibold text-gray-900 mb-3 leading-tight">${title}</h3>
                    <p class="text-gray-600 leading-relaxed mb-4 flex-1">${excerpt}</p>
                </div>
                <div class="mt-auto">
                    <a href="${url}" class="bg-white inline-flex items-center text-csu-blue hover:text-csu-light-blue font-medium transition-all duration-200 hover:translate-x-1">
                        Read full article
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        `;

        // Fade in new content
        featuredContent.style.opacity = '1';
        featuredContent.style.transform = 'translateY(0)';
    }, 150);
}

// Mobile horizontal scroll functions
function scrollNewsLeft() {
    const container = document.getElementById('news-scroll-container');
    container.scrollBy({ left: -300, behavior: 'smooth' });
}

function scrollNewsRight() {
    const container = document.getElementById('news-scroll-container');
    container.scrollBy({ left: 300, behavior: 'smooth' });
}

// Scroll progress indicator
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('news-scroll-container');
    const indicators = document.querySelectorAll('.scroll-indicator');
    
    if (container && indicators.length > 0) {
        container.addEventListener('scroll', function() {
            const scrollLeft = container.scrollLeft;
            const scrollWidth = container.scrollWidth - container.clientWidth;
            const scrollPercent = (scrollLeft / scrollWidth) * 100;
            
            // Update each indicator based on scroll position
            indicators.forEach((indicator, index) => {
                const itemPercent = (index / indicators.length) * 100;
                const nextItemPercent = ((index + 1) / indicators.length) * 100;
                
                if (scrollPercent >= itemPercent && scrollPercent < nextItemPercent) {
                    const progress = ((scrollPercent - itemPercent) / (nextItemPercent - itemPercent)) * 100;
                    indicator.style.width = progress + '%';
                } else if (scrollPercent >= nextItemPercent) {
                    indicator.style.width = '100%';
                } else {
                    indicator.style.width = '0%';
                }
            });
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    // Re-enable desktop functionality when screen becomes large
    if (window.innerWidth >= 768) {
        // Reset any mobile-specific states if needed
    }
});
</script>

    <x-footer />


</body>
</html>