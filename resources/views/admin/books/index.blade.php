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
        <div class="min-h-screen bg-gray-100 dark:bg-gray-100 flex">
            <x-admin-nav-bar/>

            <!-- Content Area -->
            <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
              

                <!-- Page Content -->
                <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="py-12 mt-1 border border-gray-200 shadow-sm py-8 bg-white rounded-lg">
                        <div class="max-w-[94%] mx-auto sm:px-6 lg:px-5">
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl w-[100%]">
                                <div class="p-6 text-gray-900" x-data="{ activeSection: 'All', showArchived: false }">
                                    <div class="flex justify-between items-center mb-6">
                                        <h1 class="text-xl font-semibold text-gray-800" x-text="showArchived ? '📚 Archived Books' : '📚 Available Books'"></h1>
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.books.index')}}" 
                                               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition">
                                               View Books
                                            </a>
                                            <button @click="showArchived = !showArchived" 
                                                    class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                                                <span x-text="showArchived ? 'View Active Books' : 'View Archived Books'"></span>
                                            </button>
                                            <a href="{{ route('admin.books.create')}}" 
                                               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition">
                                               + Add Books
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Active Books Section -->
                                    <div x-show="!showArchived">
                                        <!-- Section Filter Buttons -->
                                        @php
                                            $bookscount = [
                                                ['label' => 'All', 'section' => 'All', 'count' => $books->where('archived', false)->count(), 'desc' => 'All Books', 'descColor' => 'text-gray-500'],
                                                ['label' => 'CICS', 'section' => 'CICS', 'count' => $books->where('section', 'CICS')->where('archived', false)->count(), 'desc' => 'Information & Computing Sciences', 'descColor' => 'text-violet-500'],
                                                ['label' => 'CTED', 'section' => 'CTED', 'count' => $books->where('section', 'CTED')->where('archived', false)->count(), 'desc' => 'Teacher Education', 'descColor' => 'text-blue-500'],
                                                ['label' => 'CCJE', 'section' => 'CCJE', 'count' => $books->where('section', 'CCJE')->where('archived', false)->count(), 'desc' => 'Criminal Justice', 'descColor' => 'text-red-500'],
                                                ['label' => 'CHM', 'section' => 'CHM', 'count' => $books->where('section', 'CHM')->where('archived', false)->count(), 'desc' => 'Hospitality Management', 'descColor' => 'text-pink-400'],
                                                ['label' => 'CBEA', 'section' => 'CBEA', 'count' => $books->where('section', 'CBEA')->where('archived', false)->count(), 'desc' => 'Business & Accountancy', 'descColor' => 'text-yellow-500'],
                                                ['label' => 'CA', 'section' => 'CA', 'count' => $books->where('section', 'CA')->where('archived', false)->count(), 'desc' => 'Agriculture', 'descColor' => 'text-green-500'],
                                            ];
                                        @endphp

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                                            @foreach ($bookscount as $section)
                                                <button
                                                    @click="activeSection = '{{ $section['section'] }}'"
                                                    x-bind:class="{ 'border-gray-900 bg-gray-50': activeSection === '{{ $section['section'] }}', 'hover:bg-gray-50': activeSection !== '{{ $section['section'] }}' }"
                                                    class="w-full text-left p-4 rounded-lg border text-sm transition shadow-sm"
                                                >
                                                    <div class="font-medium text-gray-800">
                                                        {{ $section['label'] }} ({{ $section['count'] }})
                                                    </div>
                                                    <div class="text-xs {{ $section['descColor'] }} mt-1">{{ $section['desc'] }}</div>
                                                </button>
                                            @endforeach
                                        </div>

                                        <!-- Active Book Cards -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                            @foreach($books->where('archived', false) as $book)
                                                <div 
                                                    x-show="activeSection === 'All' || activeSection === '{{ $book->section }}'" 
                                                    x-transition 
                                                    class="bg-white border rounded-lg shadow-sm hover:shadow-md transition overflow-hidden"
                                                >
                                                    <div class="relative h-48 bg-gray-100">
                                                        <img src="{{ asset('storage/' . $book->image1) }}" alt="{{ $book->name }}" class="w-full h-full object-cover">
                                                        <span class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full
                                                            @if($book->section === 'CICS') bg-violet-200 text-violet-800
                                                            @elseif($book->section === 'CTED') bg-sky-200 text-sky-800
                                                            @elseif($book->section === 'CCJE') bg-red-300 text-red-800
                                                            @elseif($book->section === 'CHM') bg-pink-300 text-pink-800
                                                            @elseif($book->section === 'CBEA') bg-yellow-200 text-yellow-800
                                                            @elseif($book->section === 'CA') bg-green-300 text-green-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                            {{ $book->section }}
                                                        </span>
                                                    </div>

                                                    <div class="p-4">
                                                        <h3 class="text-lg font-medium text-gray-900">{{ $book->name }}</h3>
                                                        <p class="text-sm text-gray-500">by {{ $book->author }}</p>
                                                        <p class="text-sm text-gray-600 mt-1">Book Code: {{ $book->book_code }}</p>

                                                        @if($book->isBorrowed() && $book->currentBorrower && $book->currentBorrower->student)
                                                            <div class="mt-3 p-2 bg-yellow-50 rounded-md">
                                                                <p class="text-sm text-yellow-800">
                                                                    <span class="font-medium">Currently borrowed by:</span><br>
                                                                    {{ $book->currentBorrower->student->fname }} {{ $book->currentBorrower->student->lname }}<br>
                                                                    <span class="text-xs">({{ $book->currentBorrower->student->student_id }})</span>
                                                                </p>
                                                            </div>
                                                        @endif

                                                        <div class="flex justify-between mt-4 text-sm">
                                                            <a href="{{ route('admin.books.edit', $book->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                                            @if(!$book->isBorrowed())
                                                                <form action="{{ route('admin.books.archive', $book->id) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" 
                                                                        class="text-yellow-600 hover:text-yellow-800"
                                                                        onclick="return confirm('Are you sure you want to archive this book?')">
                                                                        Archive
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Archived Books Section -->
                                    <div x-show="showArchived" x-cloak>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                            @forelse($books->where('archived', true) as $book)
                                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $book->name }}</h3>
                                                            <p class="text-sm text-gray-600 mb-1">Author: {{ $book->author }}</p>
                                                            <p class="text-sm text-gray-600 mb-1">Section: {{ $book->section }}</p>
                                                            <p class="text-sm text-gray-600 mb-1">Book Code: {{ $book->book_code }}</p>
                                                            <p class="text-sm text-gray-500 mb-4">Archived: {{ $book->archived_at->format('M d, Y') }}</p>
                                                            
                                                            @if($book->image1)
                                                                <div class="mb-4">
                                                                    <img src="{{ asset('storage/' . $book->image1) }}" 
                                                                         alt="{{ $book->name }}" 
                                                                         class="w-full h-48 object-cover rounded-lg shadow-sm">
                                                                </div>
                                                            @endif

                                                            <div class="flex justify-end space-x-2">
                                                                <form action="{{ route('admin.books.unarchive', $book->id) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" 
                                                                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                        Unarchive
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-span-full text-center py-12">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                    </svg>
                                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No archived books</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Get started by archiving a book from the active books list.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                </main>
            </div>
        </div>
    </body>
    <!-- Delete Confirmation Modal -->
    <div 
        x-data="{ isOpen: false, formToSubmit: null }" 
        @open-delete-modal.window="isOpen = true; formToSubmit = $event.detail.form" 
        x-show="isOpen" 
        style="display: none;"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
        >
            <h2 class="text-lg font-semibold mb-4">Confirm Delete</h2>
            <p class="mb-6">Are you sure you want to delete this book?</p>
            <div class="flex justify-end space-x-4">
                <button 
                    @click="isOpen = false; formToSubmit = null" 
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
                >
                    Cancel
                </button>
                <button 
                    @click="formToSubmit.submit()" 
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load overdue books
        fetch('{{ route("admin.overdue.books") }}')
            .then(response => response.json())
            .then(books => {
                const container = document.getElementById('overdueBooksList');
                if (books.length === 0) {
                    container.innerHTML = '<p class="text-gray-500">No overdue books found.</p>';
                    return;
                }
                
                const html = books.map(book => `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium">${book.book.name}</h3>
                                <p class="text-sm text-gray-600">Borrowed by: ${book.student.fname} ${book.student.lname}</p>
                                <p class="text-sm text-gray-600">Student ID: ${book.student.student_id}</p>
                                <p class="text-sm text-red-600">Days overdue: ${book.days_overdue}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Borrowed on: ${new Date(book.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading overdue books:', error);
                document.getElementById('overdueBooksList').innerHTML = 
                    '<p class="text-red-500">Error loading overdue books. Please try again later.</p>';
            });
    });
    </script>
    @endpush
</html>
