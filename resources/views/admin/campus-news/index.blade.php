<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus News Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-scale { animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        .animate-slide-down { animation: slideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

        .delay-100 { animation-delay: 0.1s; }
        .delay-150 { animation-delay: 0.15s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-250 { animation-delay: 0.25s; }
        .delay-300 { animation-delay: 0.3s; }

        .main-content {
            transition: margin-left 0.5s ease-in-out;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="bg-gray-50">
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">
        <x-admin-nav-bar />

        <!-- Main Content -->
        <div class="min-h-screen">
            <div class="max-w-7xl mx-auto px-6 py-8">
                
                <!-- Header Section -->
                <div class="mb-10 animate-slide-down">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div class="space-y-2">
                            <h1 class="text-4xl font-semibold text-gray-900 tracking-tight">Campus News</h1>
                            <p class="text-base text-gray-500">Manage and publish campus news and announcements</p>
                        </div>
                        <a href="{{ route('admin.campus-news.create') }}" 
                           class="inline-flex items-center gap-3 bg-gradient-to-b from-gray-800 to-black text-white px-6 py-3.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>New Article</span>
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Total News -->
                    <div class="animate-slide-up delay-100 group">
                        <div class="relative overflow-hidden bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                            <div class="flex items-start justify-between">
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total News</p>
                                    <p class="text-4xl font-bold text-gray-900">{{ $news->total() }}</p>
                                </div>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                        </div>
                    </div>

                    <!-- Published -->
                    <div class="animate-slide-up delay-150 group">
                        <div class="relative overflow-hidden bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                            <div class="flex items-start justify-between">
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Published</p>
                                    <p class="text-4xl font-bold text-gray-900">{{ $news->where('status', 'published')->count() }}</p>
                                </div>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-emerald-600"></div>
                        </div>
                    </div>

                    <!-- Archived -->
                    <div class="animate-slide-up delay-200 group">
                        <div class="relative overflow-hidden bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                            <div class="flex items-start justify-between">
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Archived</p>
                                    <p class="text-4xl font-bold text-gray-900">{{ $news->where('status', 'archived')->count() }}</p>
                                </div>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-pink-600 flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-400 to-pink-600"></div>
                        </div>
                    </div>

                    <!-- Featured -->
                    <div class="animate-slide-up delay-250 group">
                        <div class="relative overflow-hidden bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                            <div class="flex items-start justify-between">
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Featured</p>
                                    <p class="text-4xl font-bold text-gray-900">{{ $news->where('is_featured', true)->count() }}</p>
                                </div>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 to-pink-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8 shadow-sm animate-slide-up delay-300">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <!-- Search and Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 flex-1">
                            <!-- Search -->
                            <div class="relative flex-1 max-w-md">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       id="searchInput"
                                       placeholder="Search articles..." 
                                       class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-300 outline-none">
                            </div>

                            <!-- Category Filter -->
                            <select id="categoryFilter" 
                                    class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-300 outline-none cursor-pointer">
                                <option value="">All Categories</option>
                                <option value="academic">Academic</option>
                                <option value="events">Events</option>
                                <option value="sports">Sports</option>
                                <option value="research">Research</option>
                                <option value="announcement">Announcement</option>
                                <option value="achievement">Achievement</option>
                            </select>

                            <!-- Status Filter -->
                            <select id="statusFilter" 
                                    class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-300 outline-none cursor-pointer">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- News Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-slide-up delay-300">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Article</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Published</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Views</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($news as $newsItem)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0">
                                                @if($newsItem->featured_image)
                                                    <img class="w-14 h-14 rounded-xl object-cover shadow-sm" src="{{ asset('storage/' . $newsItem->featured_image) }}" alt="">
                                                @else
                                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 mb-1.5">
                                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($newsItem->title, 50) }}</h3>
                                                    @if($newsItem->is_featured)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-yellow-100 to-yellow-50 text-yellow-700 rounded-full text-xs font-medium flex-shrink-0">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                            Featured
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 truncate">{{ Str::limit($newsItem->excerpt ?: strip_tags($newsItem->content), 70) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ match($newsItem->category) {
                                            'academic' => 'bg-blue-50 text-blue-700',
                                            'events' => 'bg-green-50 text-green-700',
                                            'sports' => 'bg-orange-50 text-orange-700',
                                            'research' => 'bg-purple-50 text-purple-700',
                                            'achievement' => 'bg-yellow-50 text-yellow-700',
                                            default => 'bg-gray-50 text-gray-700'
                                        } }}">
                                            {{ ucfirst($newsItem->category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium {{ match($newsItem->status) {
                                            'published' => 'bg-green-50 text-green-700',
                                            'draft' => 'bg-yellow-50 text-yellow-700',
                                            'archived' => 'bg-gray-50 text-gray-700',
                                            default => 'bg-gray-50 text-gray-700'
                                        } }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ match($newsItem->status) {
                                                'published' => 'bg-green-500',
                                                'draft' => 'bg-yellow-500',
                                                'archived' => 'bg-gray-500',
                                                default => 'bg-gray-500'
                                            } }}"></span>
                                            {{ ucfirst($newsItem->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-700">{{ $newsItem->publish_date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>{{ number_format($newsItem->views_count) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-5">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No news articles yet</h3>
                                            <p class="text-sm text-gray-500 mb-6 max-w-sm">Get started by creating your first campus news article to share with your community</p>
                                            <a href="{{ route('admin.campus-news.create') }}" 
                                               class="inline-flex items-center gap-2 bg-gradient-to-b from-gray-800 to-black text-white px-5 py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Create News
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($news->hasPages())
                    <div class="bg-gray-50/50 px-6 py-4 flex items-center justify-between border-t border-gray-100">
                        <div class="flex justify-between flex-1 sm:hidden">
                            @if($news->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $news->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    Previous
                                </a>
                            @endif

                            @if($news->hasMorePages())
                                <a href="{{ $news->nextPageUrl() }}" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    Next
                                </a>
                            @else
                                <span class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg">
                                    Next
                                </span>
                            @endif
                        </div>

                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-600">
                                    Showing <span class="font-semibold text-gray-900">{{ $news->firstItem() }}</span>
                                    to <span class="font-semibold text-gray-900">{{ $news->lastItem() }}</span>
                                    of <span class="font-semibold text-gray-900">{{ $news->total() }}</span> results
                                </p>
                            </div>
                            <div>
                                {{ $news->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const tableRows = document.querySelectorAll('tbody tr:not(:last-child)');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const categoryValue = categoryFilter.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();

                let visibleCount = 0;

                tableRows.forEach(row => {
                    if (row.querySelector('td[colspan]')) return;

                    const titleEl = row.querySelector('td:nth-child(1) h3');
                    const excerptEl = row.querySelector('td:nth-child(1) p');
                    const categoryEl = row.querySelector('td:nth-child(2) span');
                    const statusEl = row.querySelector('td:nth-child(3) span');

                    const title = titleEl?.textContent.toLowerCase() || '';
                    const excerpt = excerptEl?.textContent.toLowerCase() || '';
                    const category = categoryEl?.textContent.toLowerCase() || '';
                    const status = statusEl?.textContent.toLowerCase() || '';

                    const matchesSearch = title.includes(searchTerm) || excerpt.includes(searchTerm);
                    const matchesCategory = !categoryValue || category.includes(categoryValue);
                    const matchesStatus = !statusValue || status.includes(statusValue);

                    if (matchesSearch && matchesCategory && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            categoryFilter.addEventListener('change', filterTable);
            statusFilter.addEventListener('change', filterTable);
        });
    </script>
</body>
</html>