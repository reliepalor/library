<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus News Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .apple-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .apple-button {
            border-radius: 2rem;
            box-shadow: 0 2px 10px rgba(0, 122, 255, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .apple-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(178, 178, 178, 0.4);
        }
        .apple-input {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }
        .apple-input:focus {
            border-color: #007aff;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }
        .apple-icon {
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }

        .main-content {
            transition: margin-left 0.5s ease-in-out;
        }
        .sidebar-collapsed {
            margin-left: 4rem;
        }
        .sidebar-expanded {
            margin-left: 15rem;
        }
        @media (max-width: 768px) {
            .sidebar-collapsed, .sidebar-expanded {
                margin-left: 0;
            }
        }

        #dropdownButton[aria-expanded="true"] svg {
            transform: rotate(180deg);
        }
        a:hover svg {
            color: #007aff;
        }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

    <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="flex h-screen" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <x-admin-nav-bar />

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto" :class="sidebarExpanded ? 'sidebar-expanded' : 'sidebar-collapsed'">
            <div class="container mx-auto px-4 py-8">
                <div class="min-h-screen bg-gradient-to-br">
    <!-- Header -->
    <div class="apple-card sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg hidden">

                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Campus News</h1>
                        <p class="text-gray-600">Manage and publish campus news and announcements</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.campus-news.create') }}"
                       class="apple-button bg-gray-800 text-white px-6 py-3 hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                        <svg class="h-5 w-5 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add News</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="apple-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total News</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $news->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-blue-600 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="apple-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Published</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $news->where('status', 'published')->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-green-600 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="apple-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Drafts</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $news->where('status', 'draft')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-yellow-600 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="apple-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Featured</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $news->where('is_featured', true)->count() }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-purple-600 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="apple-card p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text"
                               placeholder="Search news..."
                               class="apple-input w-full sm:w-64 pl-10 pr-4 py-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <select class="apple-input w-full sm:w-48 px-3 py-2">
                        <option value="">All Categories</option>
                        <option value="academic">Academic</option>
                        <option value="events">Events</option>
                        <option value="sports">Sports</option>
                        <option value="research">Research</option>
                        <option value="announcement">Announcement</option>
                        <option value="achievement">Achievement</option>
                    </select>

                    <!-- Status Filter -->
                    <select class="apple-input w-full sm:w-32 px-3 py-2">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="h-5 w-5 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="h-5 w-5 apple-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- News Table -->
        <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg overflow-hidden border border-blue-200/40">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                News
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Publish Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Views
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($news as $newsItem)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($newsItem->featured_image)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $newsItem->featured_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 2H9a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-1"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <h3 class="text-sm font-medium text-gray-900">{{ Str::limit($newsItem->title, 40) }}</h3>
                                            @if($newsItem->is_featured)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                        <path d="M4 0L5.09 2.26L8 3L5.09 3.74L4 6L2.91 3.74L0 3L2.91 2.26L4 0Z"/>
                                                    </svg>
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ Str::limit($newsItem->excerpt ?: strip_tags($newsItem->content), 50) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ match($newsItem->category) {
                                    'academic' => 'bg-blue-100 text-blue-800',
                                    'events' => 'bg-green-100 text-green-800',
                                    'sports' => 'bg-orange-100 text-orange-800',
                                    'research' => 'bg-purple-100 text-purple-800',
                                    'achievement' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                    {{ ucfirst($newsItem->category) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ match($newsItem->status) {
                                    'published' => 'bg-green-100 text-green-800',
                                    'draft' => 'bg-yellow-100 text-yellow-800',
                                    'archived' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                    {{ ucfirst($newsItem->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $newsItem->publish_date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($newsItem->views_count) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('admin.campus-news.show', $newsItem) }}"
                                       class="text-blue-600 hover:text-blue-900 p-1 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.campus-news.edit', $newsItem) }}"
                                       class="text-indigo-600 hover:text-indigo-900 p-1 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete({{ $newsItem->id }})"
                                            class="text-red-600 hover:text-red-900 p-1 rounded-lg hover:bg-red-50 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 2H9a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-1"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No news found</h3>
                                    <p class="text-gray-500 mb-4">Get started by creating your first campus news article.</p>
                                    <a href="{{ route('admin.campus-news.create') }}"
                                       class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:from-gray-500 hover:to-gray-700 transition-all duration-300">
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
            <div class="bg-white/80 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex justify-between flex-1 sm:hidden">
                    @if($news->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                            Previous
                        </span>
                    @else
                        <a href="{{ $news->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if($news->hasMorePages())
                        <a href="{{ $news->nextPageUrl() }}" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                            Next
                        </span>
                    @endif
                </div>

                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $news->firstItem() }}</span>
                            to <span class="font-medium">{{ $news->lastItem() }}</span>
                            of <span class="font-medium">{{ $news->total() }}</span> results
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
</body>

<script>
function confirmDelete(newsId) {
    if (confirm('Are you sure you want to delete this news article? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/campus-news/${newsId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</html>

