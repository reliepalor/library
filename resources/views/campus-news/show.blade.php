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
    <nav class="flex mb-8" aria-label="Breadcrumb">
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
            {!! nl2br(e($campusNews->content)) !!}
        </div>
    </article>

    <!-- Article Footer -->
    <footer class="mt-12 pt-8 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-0">
                <i class="fas fa-eye mr-2"></i>
                {{ $campusNews->views_count }} views
            </div>

            <div class="flex space-x-4">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>

                <button onclick="shareArticle()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-csu-blue hover:bg-csu-light-blue">
                    <i class="fas fa-share mr-2"></i>
                    Share
                </button>
            </div>
        </div>
    </footer>

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
    function shareArticle() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $campusNews->title }}',
                text: '{{ $campusNews->excerpt }}',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link copied to clipboard!');
            });
        }
    }
</script>

</body>
</html>