<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Campus News - CSU Library Admin</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/campus-news.js'])

        <style>
        body {
            font-family: 'Inter', sans-serif;
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

            @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.3s ease-out forwards; }
    #dropdownMenu.show {
        transform: scaleY(1);
        opacity: 1;
        display: block;
    }
    #dropdownButton[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
    a:hover svg {
        color: #3B82F6;
    }
    </style>
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
<body class="bg-gray-50">
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">

        <x-admin-nav-bar />
        <div class="min-h-screen">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-lg border-b border-blue-200/40 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">Edit Campus News</h1>
                                <p class="text-gray-600">Update news and announcements for the campus community</p>
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
                <form action="{{ route('admin.campus-news.update', $campusNews) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

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
                                    value="{{ old('title', $campusNews->title) }}"
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
                                    <option value="academic" {{ old('category', $campusNews->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="events" {{ old('category', $campusNews->category) == 'events' ? 'selected' : '' }}>Events</option>
                                    <option value="sports" {{ old('category', $campusNews->category) == 'sports' ? 'selected' : '' }}>Sports</option>
                                    <option value="research" {{ old('category', $campusNews->category) == 'research' ? 'selected' : '' }}>Research</option>
                                    <option value="announcement" {{ old('category', $campusNews->category) == 'announcement' ? 'selected' : '' }}>Announcement</option>
                                    <option value="achievement" {{ old('category', $campusNews->category) == 'achievement' ? 'selected' : '' }}>Achievement</option>
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
                                    <option value="published" {{ old('status', $campusNews->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $campusNews->status) == 'archived' ? 'selected' : '' }}>Archived</option>
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
                                    value="{{ old('publish_date', $campusNews->publish_date->format('Y-m-d\TH:i')) }}"
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
                                    {{ old('is_featured', $campusNews->is_featured) ? 'checked' : '' }}
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
                                    placeholder="Brief summary of the news article...">{{ old('excerpt', $campusNews->excerpt) }}</textarea>
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
                                    required>{{ old('content', $campusNews->content) }}</textarea>
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

                        <!-- Current Image -->
                        @if($campusNews->featured_image)
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-700 mb-2">Current Image</p>
                            <div class="relative inline-block">
                                <img src="{{ asset('storage/' . $campusNews->featured_image) }}"
                                    alt="{{ $campusNews->title }}"
                                    class="h-32 w-48 object-cover rounded-lg border border-gray-300">
                                <button type="button"
                                        onclick="removeCurrentImage()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="remove_featured_image" id="removeFeaturedImage" value="0">
                        </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <div class="space-y-4">
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                                    </svg>
                                </div>
                                <div>
                                    <label for="featured_image" class="cursor-pointer">
                                        <span class="mt-2 block text-sm font-medium text-gray-900">Upload new featured image</span>
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
                            <p class="text-sm font-medium text-gray-700 mb-2">New Image Preview</p>
                            <img id="previewImg" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg mx-auto">
                        </div>

                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Action Buttons -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                        <div class="flex flex-col sm:flex-row sm:justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('admin.campus-news.show', $campusNews) }}"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-300 text-center">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Update News</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>




</body>
</html>
