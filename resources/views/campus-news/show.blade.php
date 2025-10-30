<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $campusNews->title }} - CSU Library</title>
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

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 mt-10" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-csu-blue">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Campus News</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ Str::limit($campusNews->title, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Article Header -->
    <header class="mb-8">
        <div class="flex items-center text-sm text-gray-500 mb-4">
            <i class="far fa-calendar-alt mr-2"></i>
            {{ $campusNews->publish_date->format('M d, Y') }}
            @if($campusNews->category)
                <span class="mx-2">•</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $campusNews->category_color }}-100 text-{{ $campusNews->category_color }}-800">
                    {{ ucfirst($campusNews->category) }}
                </span>
            @endif
            @if($campusNews->author_name)
                <span class="mx-2">•</span>
                <i class="fas fa-user mr-1"></i>
                {{ $campusNews->author_name }}
            @endif
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $campusNews->title }}</h1>

        @if($campusNews->featured_image)
            <div class="aspect-w-16 aspect-h-9 mb-6">
                <img src="{{ asset('storage/' . $campusNews->featured_image) }}" alt="{{ $campusNews->title }}" class="w-full h-64 md:h-96 object-cover rounded-lg shadow-lg">
            </div>
        @endif
    </header>

    <!-- Article Content -->
    <article class="prose prose-lg max-w-none">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
            <div id="article-content" class="relative">
                <div id="cropped-content" class="overflow-hidden relative">
                    {!! nl2br(e(Str::limit($campusNews->content, 500))) !!}
                    @if(strlen($campusNews->content) > 500)
                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                    @endif
                </div>
                <div id="full-content" class="hidden">
                    {!! nl2br(e($campusNews->content)) !!}
                </div>
                @if(strlen($campusNews->content) > 500)
                    <div class="text-center mt-4">
                        <button id="see-more-btn" onclick="toggleContent()" class="text-csu-blue hover:text-csu-light-blue font-medium transition-colors duration-200">
                            See more <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </article>

    <!-- Related News -->
    @php
        $relatedNews = \App\Models\CampusNews::where('status', 'published')
            ->where('id', '!=', $campusNews->id)
            ->where('category', $campusNews->category)
            ->orderBy('publish_date', 'desc')
            ->limit(3)
            ->get();
    @endphp

    @if($relatedNews->count() > 0)
        <section class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related News</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedNews as $related)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        @if($related->featured_image)
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-32 object-cover">
                            </div>
                        @endif
                        <div class="p-4">
                            <div class="flex items-center text-xs text-gray-500 mb-2">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $related->publish_date->format('M d, Y') }}
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $related->title }}</h3>
                            <a href="{{ route('campus-news.show', $related) }}" class="text-csu-blue hover:text-csu-light-blue font-medium text-sm">
                                Read more <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<x-footer />

<script>
    function toggleContent() {
        const croppedContent = document.getElementById('cropped-content');
        const fullContent = document.getElementById('full-content');
        const seeMoreBtn = document.getElementById('see-more-btn');
        const icon = seeMoreBtn.querySelector('i');

        if (fullContent.classList.contains('hidden')) {
            // Show full content
            croppedContent.classList.add('hidden');
            fullContent.classList.remove('hidden');
            seeMoreBtn.innerHTML = 'See less <i class="fas fa-chevron-up ml-1"></i>';
        } else {
            // Show cropped content
            fullContent.classList.add('hidden');
            croppedContent.classList.remove('hidden');
            seeMoreBtn.innerHTML = 'See more <i class="fas fa-chevron-down ml-1"></i>';
        }
    }
</script>

</body>
</html>