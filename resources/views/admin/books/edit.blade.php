<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin | Books</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Smooth transitions for the sidebar */
            [x-cloak] { display: none !important; }
            
            /* Custom nav link styling for the sidebar */
            .nav-link {
                display: flex;
                align-items: center;
                padding: 0.75rem 1rem;
                color: #4b5563;
                transition: all 0.3s ease;
            }
            
            .nav-link:hover {
                background-color: #f3f4f6;
            }
            
            .nav-link.active {
                background-color: #e5e7eb;
                color: #111827;
                border-left: 3px solid #3b82f6;
            }
            
            /* Ensure smooth transition for content area */
            .content-area {
                transition: margin-left 0.3s ease;
            }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <x-admin-nav-bar/>
        <div class="mt-[7rem]">
            <div class="flex justify-center mt-5 ">

                <form action="{{ route('admin.books.update', $books->id) }}" method="POST" enctype="multipart/form-data" class="w-[80%] mx-auto p-8 bg-white rounded-lg shadow-md">
                    @csrf
                    @method('PUT')
            
                    <div class="flex justify-center gap-8">
                        <div class="border border-gray-200 shadow-md px-10 py-5 rounded-lg">
                        

                            <!-- Book Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Book Name</label>
                                <input type="text" name="name" id="name" placeholder="Enter book name" 
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required 
                                value="{{ old('name', $books->name) }}">
                            </div>
                        
                            <!-- Author -->
                            <div class="mb-4">
                                <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                                <input type="text" name="author" id="author" placeholder="Enter author's name" 
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required
                                value="{{ old('author', $books->author) }}">
                            </div>
                        
                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="4" 
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required>{{ old('description', $books->description) }}</textarea>
                            </div>
                        
                            <!-- Section -->
                            <div class="mb-4">
                                <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                                <select name="section" id="section" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="" disabled>Select Section</option>
                                    <option value="CICS" {{ old('section', $books->section) == 'CICS' ? 'selected' : '' }}>CICS</option>
                                    <option value="CTED" {{ old('section', $books->section) == 'CTED' ? 'selected' : '' }}>CTED</option>
                                    <option value="CCJE" {{ old('section', $books->section) == 'CCJE' ? 'selected' : '' }}>CCJE</option>
                                    <option value="CHM" {{ old('section', $books->section) == 'CHM' ? 'selected' : '' }}>CHM</option>
                                    <option value="CBEA" {{ old('section', $books->section) == 'CBEA' ? 'selected' : '' }}>CBEA</option>
                                    <option value="CA" {{ old('section', $books->section) == 'CA' ? 'selected' : '' }}>CA</option>
                                </select>
                            </div>
                        </div>
                        <div class="border border-gray-200 shadow-md px-10 py-5 rounded-lg">
                            <div class="mb-4 space-y-4">
                                <!-- Image 1 -->
                                <div class="flex flex-row gap-5 shadow-lg rounded-lg items-center p-2 justify-center">
                                    @if ($books->image1)
                                        <img src="{{ asset('storage/' . $books->image1) }}" alt="Image 1" class="w-20 h-20 mb-2 rounded-md shadow">
                                    @endif
                                    <label for="image1" class="block text-sm font-medium text-gray-700">Image 1</label>
                                    <input type="file" name="image1" id="image1" 
                                    class="mt-1 block w-full h-10 p-1 text-gray-500 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>
                            
                                <!-- Image 2 -->
                                <div class="flex flex-row gap-5 shadow-lg rounded-lg items-center p-2 justify-center">
                                    @if ($books->image2)
                                        <img src="{{ asset('storage/' . $books->image2) }}" alt="Image 2" class="w-20 h-20 mb-2 rounded-md shadow">
                                    @endif
                                    <label for="image2" class="block text-sm font-medium text-gray-700">Image 2 (Optional)</label>
                                    <input type="file" name="image2" id="image2" 
                                    class="mt-1 block w-full h-10 p-1 text-gray-500 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>
                            
                                <!-- Image 3 -->
                                <div class="flex flex-row gap-5 shadow-lg rounded-lg items-center p-2 justify-center">
                                    @if ($books->image3)
                                        <img src="{{ asset('storage/' . $books->image3) }}" alt="Image 3" class="w-20 h-20 mb-2 rounded-md shadow">
                                    @endif
                                    <label for="image3" class="block text-sm font-medium text-gray-700">Image 3 (Optional)</label>
                                    <input type="file" name="image3" id="image3" 
                                    class="mt-1 block w-full h-10 p-1 text-gray-500 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>
                            
                                <!-- Image 4 -->
                                <div class="flex flex-row gap-5 shadow-lg rounded-lg items-center p-2 justify-center">
                                    @if ($books->image4)
                                        <img src="{{ asset('storage/' . $books->image4) }}" alt="Image 4" class="w-20 h-20 mb-2 rounded-md shadow">
                                    @endif
                                    <label for="image4" class="block text-sm font-medium text-gray-700">Image 4 (Optional)</label>
                                    <input type="file" name="image4" id="image4" 
                                    class="mt-1 block w-full h-10 p-1 text-gray-500 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>
                            
                                <!-- Image 5 -->
                                <div class="flex flex-row gap-5 shadow-lg rounded-lg items-center p-2 justify-center">
                                    @if ($books->image5)
                                        <img src="{{ asset('storage/' . $books->image5) }}" alt="Image 5" class="w-20 h-20 mb-2 rounded-md shadow">
                                    @endif
                                    <label for="image5" class="block text-sm font-medium text-gray-700">Image 5 (Optional)</label>
                                    <input type="file" name="image5" id="image5" 
                                    class="mt-1 block w-full h-10 p-1 text-gray-500 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                
            
                <!-- Image Uploads -->
        
            
                <!-- Optional Authors Inputs -->
                <div class="space-y-4 mb-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Additional Authors (Optional)</h3>
                    @for ($i = 1; $i <= 5; $i++)
                        <div>
                            <label for="author_number{{ $i }}" class="block text-sm font-medium text-gray-700">Author {{ $i }}</label>
                            <input type="text" name="author_number{{ $i }}" id="author_number{{ $i }}" 
                                placeholder="Enter author name"
                                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                value="{{ old('author_number'.$i, $books->{'author_number'.$i}) }}">
                        </div>
                    @endfor
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Update Book
                    </button>
                </div>
            </form>
            
            </div>
        </div>
    </body>
</html>

