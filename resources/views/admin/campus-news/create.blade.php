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
        }
        .shadcn-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
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
        color: #3B82F6;
    }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

    <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <x-admin-nav-bar />

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto" :class="sidebarExpanded ? 'sidebar-expanded' : 'sidebar-collapsed'">
            <div class="container mx-auto px-4 py-8">
                <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
                    <!-- Header -->
                    <div class="bg-white/80 backdrop-blur-lg border-b border-blue-200/40 sticky top-0 z-40">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-900">Create Campus News</h1>
                                        <p class="text-gray-600">Publish news and announcements for the campus community</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.campus-news.index') }}"
                                    class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0l-7 7"/>
                                        </svg>
                                        <span>Back to News</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <form action="{{ route('admin.campus-news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                            @csrf

                            <!-- Basic Information -->
                            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Basic Information
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Title -->
                                    <div class="md:col-span-2">
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                            Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                            id="title"
                                            name="title"
                                            value="{{ old('title') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('title') border-red-500 @enderror"
                                            placeholder="Enter news title..."
                                            required>
                                        @error('title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                            Category <span class="text-red-500">*</span>
                                        </label>
                                        <select id="category"
                                                name="category"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('category') border-red-500 @enderror"
                                                required>
                                            <option value="">Select Category</option>
                                            <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                                            <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>Events</option>
                                            <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                                            <option value="research" {{ old('category') == 'research' ? 'selected' : '' }}>Research</option>
                                            <option value="announcement" {{ old('category') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                                            <option value="achievement" {{ old('category') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                                        </select>
                                        @error('category')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select id="status"
                                                name="status"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('status') border-red-500 @enderror"
                                                required>
                                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Publish Date -->
                                    <div>
                                        <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Publish Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="datetime-local"
                                            id="publish_date"
                                            name="publish_date"
                                            value="{{ old('publish_date', now()->format('Y-m-d\TH:i')) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('publish_date') border-red-500 @enderror"
                                            required>
                                        @error('publish_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Featured -->
                                    <div class="flex items-center">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox"
                                            id="is_featured"
                                            name="is_featured"
                                            value="1"
                                            {{ old('is_featured') ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                            Mark as Featured
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Content
                                </h2>

                                <!-- Excerpt -->
                                <div class="mb-6">
                                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                        Excerpt <span class="text-gray-500">(Optional)</span>
                                    </label>
                                    <textarea id="excerpt"
                                            name="excerpt"
                                            rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('excerpt') border-red-500 @enderror"
                                            placeholder="Brief summary of the news article...">{{ old('excerpt') }}</textarea>
                                    @error('excerpt')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Content <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="content"
                                            name="content"
                                            rows="12"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('content') border-red-500 @enderror"
                                            placeholder="Write your news content here..."
                                            required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Media -->
                            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Featured Image
                                </h2>

                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                    <div class="space-y-4">
                                        <div class="mx-auto h-12 w-12 text-gray-400">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label for="featured_image" class="cursor-pointer">
                                                <span class="mt-2 block text-sm font-medium text-gray-900">Upload featured image</span>
                                                <span class="mt-1 block text-xs text-gray-500">PNG, JPG, GIF up to 10MB</span>
                                            </label>
                                            <input id="featured_image"
                                                name="featured_image"
                                                type="file"
                                                class="sr-only"
                                                accept="image/*"
                                                onchange="previewImage(this)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-4 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg mx-auto">
                                </div>

                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SEO Settings -->
                            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    SEO Settings
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Meta Title -->
                                    <div class="md:col-span-2">
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Title <span class="text-gray-500">(Optional)</span>
                                        </label>
                                        <input type="text"
                                            id="meta_title"
                                            name="meta_title"
                                            value="{{ old('meta_title') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="SEO title for search engines...">
                                        <p class="mt-1 text-xs text-gray-500">Leave empty to use the news title</p>
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="md:col-span-2">
                                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Description <span class="text-gray-500">(Optional)</span>
                                        </label>
                                        <textarea id="meta_description"
                                                name="meta_description"
                                                rows="3"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="Brief description for search engines...">{{ old('meta_description') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                                    </div>

                                    <!-- Tags -->
                                    <div class="md:col-span-2">
                                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tags <span class="text-gray-500">(Optional)</span>
                                        </label>
                                        <input type="text"
                                            id="tags"
                                            name="tags"
                                            value="{{ old('tags') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="tag1, tag2, tag3">
                                        <p class="mt-1 text-xs text-gray-500">Separate tags with commas</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                                <div class="flex flex-col sm:flex-row sm:justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                                    <a href="{{ route('admin.campus-news.index') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-300 text-center">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Publish News</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }

    // Auto-resize textareas
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    });
    </script>

</html>
