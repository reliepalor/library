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

    <div id="home" class="main mt-16">

        <main id="home" class="mt-1">
            <section class="big-hero" style="font-size: 65px;">The Future of <br> Cagayan State University</section>
            <section class="small-hero">Discover our new technology! It’s simple, smart, and designed to help you learn better. </section>
        </main>

        <div class="flex justify-center">
            <div class="container items-center">
                <div class="item item-1"><img src="images/library-images/books2.jpg" alt=""></div>
                <div class="item item-2"><img src="images/library-images/books1.jpg" alt=""></div>
                <div class="item item-3">
                    <div class="hero-btn">
                        <a href="" class="explore">Explore</a>
                    </div>
                    <div>
                        <img src="images/library-images/books4.jpg" alt="">
                    </div>
                </div>
                <div class="item item-4"><img src="images/library-images/books5.jpg" style="aspect-ratio: 1/4;" alt=""></div>
                <div class="item item-5"><img src="images/library-images/books3.jpg" alt=""></div>
            </div>
        </div>
    </div>

    <x-books-component/>
<!-- Campus News Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-semibold text-gray-900 mb-4">Campus News</h2>
            <p class="text-lg text-gray-600">Stay updated with the latest happenings at CSU Gonzaga Campus</p>
        </div>
        
        <!-- Two Column Layout -->
        <div class="flex gap-8 h-[700px]">
            <!-- Left Side - Featured News (70%) -->
            <div class="w-[70%]">
                <div id="featured-news" class="bg-white rounded-2xl border border-gray-200/60 overflow-hidden h-full transition-all duration-500 ease-out shadow-sm hover:shadow-lg flex flex-col">
                    @if(isset($campusNews) && count($campusNews) > 0)
                        <div class="featured-content opacity-100 transition-opacity duration-300 flex flex-col h-full">
                            @if($campusNews[0]->featured_image)
                                <div class="flex-shrink-0 bg-gray-100 h-80">
                                    <img src="{{ asset('storage/' . $campusNews[0]->featured_image) }}" 
                                         alt="{{ $campusNews[0]->title }}" 
                                         class="w-full h-full object-contain transition-transform duration-700 ease-out rounded-xl">
                                </div>
                            @endif
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                    <span class="font-medium">{{ $campusNews[0]->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mb-3 ">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($campusNews[0]->category ?? 'Announcement') }}
                                    </span>
                                    @if($campusNews[0]->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                                <div class="">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-3 leading-tight">{{ $campusNews[0]->title }}</h3>
                                    <p class="text-gray-600 leading-relaxed mb-4 flex-1">{{ $campusNews[0]->excerpt }}</p>
                                </div>
                               
                                <div class="mt-auto">
                                    <a href="{{ route('campus-news.show', $campusNews[0]) }}" 
                                       class="inline-flex items-center text-csu-blue hover:text-csu-light-blue font-medium transition-all duration-200 hover:translate-x-1">
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
                    <div class="p-6 border-b border-gray-100/80">
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
                                <div class="p-4">
                                    <div class="flex gap-3">
                                        @if($news->featured_image)
                                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $news->featured_image) }}" 
                                                     alt="{{ $news->title }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                                <i class="fas fa-newspaper text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h5 class="font-medium text-gray-900 text-sm line-clamp-2 mb-1">{{ $news->title }}</h5>
                                            <p class="text-xs text-gray-500 mb-2">{{ $news->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-600 line-clamp-2">{{ $news->excerpt }}</p>
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
    </div>
</section>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .aspect-w-16 {
        position: relative;
        width: 100%;
    }
    .aspect-w-16::before {
        content: "";
        display: block;
        padding-top: 56.25%;
    }
    .aspect-h-9 > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Apple-inspired scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.2);
    }
</style>

<script>
function showFeaturedNews(index, clickedElement) {
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
        // Update content
        const imageHtml = image ? 
            `<div class="flex-shrink-0 bg-gray-100 h-80">
                <img src="${image}" alt="${title}" class="w-full h-full object-contain transition-transform duration-700 ease-out">
             </div>` : '';
        
        const categoryBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${category.charAt(0).toUpperCase() + category.slice(1)}</span>`;
        const featuredBadge = isFeatured ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-star mr-1"></i>Featured</span>` : '';
        const badgesHtml = `<div class="flex flex-wrap gap-2 mb-3">${categoryBadge}${isFeatured ? ' ' + featuredBadge : ''}</div>`;

        featuredContent.innerHTML = `
            ${imageHtml}
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex items-center text-sm text-gray-500 mb-3">
                    <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                    <span class="font-medium">${date}</span>
                </div>
                ${badgesHtml}
                <h3 class="text-xl font-semibold text-gray-900 mb-3 leading-tight">${title}</h3>
                <p class="text-gray-600 leading-relaxed mb-4 flex-1">${excerpt}</p>
                <div class="mt-auto">
                    <a href="${url}" class="inline-flex items-center text-csu-blue hover:text-csu-light-blue font-medium transition-all duration-200 hover:translate-x-1">
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
}</script>

    <x-footer />


</body>
</html>