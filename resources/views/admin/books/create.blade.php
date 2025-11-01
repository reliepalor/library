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
    <body class="font-sans antialiased bg-gray-50">
        <div id="main-content" class="transition-all duration-500 ml-64 main-content">
            <x-admin-nav-bar/>
            <div class="flex justify-center min-h-screen bg-gray-50 mr-20">
                <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-3xl p-6 sm:p-8 my-12 bg-white rounded-2xl shadow-sm">
                    @csrf
                    <div class="space-y-10">
                        <!-- 📘 Book Details -->
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900">Book Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Book Name</label>
                                    <input type="text" name="name" id="name" placeholder="Enter book name" required
                                        class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-200 transition">
                                </div>

                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                                    <input type="text" name="author" id="author" placeholder="Enter author's name" required
                                        class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-200 transition">
                                </div>

                                <!-- Optional Authors Toggle -->
                                <div>
                                    <button type="button" id="toggleAuthorsBtn" class="text-sm font-medium text-gray-900 hover:text-gray-700 transition">
                                        Add More Authors
                                    </button>
                                </div>

                                <!-- Optional Author Inputs -->
                                <div id="optionalAuthors" class="hidden space-y-4">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div>
                                            <label for="author_number{{ $i }}" class="block text-sm font-medium text-gray-700">Author {{ $i }}</label>
                                            <input type="text" name="author_number{{ $i }}" id="author_number{{ $i }}" placeholder="Enter author name"
                                                class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-200 transition">
                                        </div>
                                    @endfor
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="4" placeholder="Enter book description" 
                                            class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-200 transition"></textarea>
                                </div>

                                <div>
                                    <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                                    <select name="section" id="section" required
                                            class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-200 transition">
                                        <option value="" disabled selected>Select Section</option>
                                        <option value="CICS">CICS</option>
                                        <option value="CTED">CTED</option>
                                        <option value="CCJE">CCJE</option>
                                        <option value="CHM">CHM</option>
                                        <option value="CBEA">CBEA</option>
                                        <option value="CA">CA</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 📸 Image Uploads -->
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900">Images</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div>
                                        <label for="image{{ $i }}" class="block text-sm font-medium text-gray-700">
                                            Image {{ $i }}{{ $i === 1 ? '' : ' (Optional)' }}
                                        </label>
                                        <input type="file" name="image{{ $i }}" id="image{{ $i }}"
                                            class="mt-1 block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm text-gray-500 file:bg-gray-50 file:border-0 file:rounded-lg file:py-2 file:px-3 file:text-sm file:text-gray-700 hover:file:bg-gray-100 transition" {{ $i === 1 ? 'required' : '' }}>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- ✅ Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-6 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-200 transition">
                                Create Book
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <script>
        document.getElementById('toggleAuthorsBtn').addEventListener('click', function() {
            const optionalAuthors = document.getElementById('optionalAuthors');
            optionalAuthors.classList.toggle('hidden');
            this.textContent = optionalAuthors.classList.contains('hidden') ? 'Add More Authors' : 'Hide Additional Authors';
        });
    </script>
    </body>
   
</html>

