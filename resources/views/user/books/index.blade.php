<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Available Books - CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/favicon/library.png" />
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/heroicons@2.0.16/dist/20/outline.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://unpkg.com/heroicons@2.0.16/dist/24/outline/search.mjs" type="module"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom Gradient Background */
        body {
            min-height: 100vh;
        }
        /* Smooth Scroll Behavior */
        html {
            scroll-behavior: smooth;
        }
        /* Fade-in Animation for Main Content */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease-out forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Hover Scale Effect */
        .hover-scale {
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
            color: #4f46e5; /* Indigo for hover */
        }
        /* Card Hover Effect */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        /* Scroll-Triggered Animation */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* Custom Dropdown Transition */
        .dropdown-transition {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Custom Section Tag Colors */
        .section-cics { background: #e5e7eb; color: #1f2937; }
        .section-cted { background: #d1d5db; color: #1f2937; }
        .section-ccje { background: #d4d4d8; color: #1f2937; }
        .section-chm { background: #d1d5db; color: #1f2937; }
        .section-cbea { background: #e5e7eb; color: #1f2937; }
        .section-ca { background: #d4d4d8; color: #1f2937; }
        .section-default { background: #e5e7eb; color: #1f2937; }
        /* Filter Button Styling */
        .filter-button {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .filter-button.active {
            background-color: #4f46e5; /* Indigo for active filter */
            color: #ffffff;
        }
        /* Minimalist Button Styling */
        .minimal-button {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .minimal-button:hover {
            background-color: #4f46e5; /* Indigo on hover */
            color: #ffffff;
        }

            .card-hover:hover {
        background-color: rgba(255, 255, 255, 0.9);
    }
    .scroll-reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .scroll-reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

        .scroll-reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .scroll-reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }
        
    </style>
</head>
<body x-data="{ selectedFilter: 'all', showReserveModal: false, reserveBookId: null }">

    <x-header />
    <section class="relative bg-gradient-to-br from-indigo-50 via-white to-blue-50 py-12 sm:py-16 px-4 sm:px-6 lg:px-8 overflow-hidden mt-10">
    <div class="absolute inset-0 bg-[url('/images/book-pattern.png')] opacity-10 bg-repeat"></div>
    <div class="relative max-w-7xl mx-auto text-center">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 sm:mb-6 
                   scroll-reveal" 
             x-data="{ visible: false }" 
             x-intersect="visible = true" 
             :class="{ 'visible': visible }">
            Discover Your Next Great Read
        </h1>
        <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto mb-6 sm:mb-8 
                  scroll-reveal" 
             x-data="{ visible: false }" 
             x-intersect="visible = true" 
             :class="{ 'visible': visible }">
            Explore our curated collection of books across various genres and sections. 
            From academic texts to captivating stories, find the perfect book to inspire, educate, and entertain.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('user.books.index') }}" 
               class="inline-block px-6 py-3 bg-indigo-600 text-white text-sm sm:text-base font-medium 
                      rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-300 
                      scroll-reveal" 
               x-data="{ visible: false }" 
               x-intersect="visible = true" 
               :class="{ 'visible': visible }">
                Browse Books
            </a>
          
        </div>
    </div>
    </section>

    <div class="max-w-7xl mx-auto py-1 px-4 sm:px-6 lg:px-8 fade-in ">
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-full shadow bg-gray-100 p-1">
                <button id="library-books-btn" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white text-gray-700 bg-white shadow active" style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);">Library Book</button>
                <button id="ebook-btn" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white text-gray-500 bg-transparent">E-Book</button>
            </div>
        </div>
<div id="library-books-container" class="px-4 sm:px-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 scroll-reveal" 
            x-data="{ visible: false }" 
            x-intersect="visible = true" 
            :class="{ 'visible': visible }">Available Books</h1>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input id="library-search-input" type="text" placeholder="Search books..." 
                       class="w-full pr-10 px-4 py-2 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm 
                              focus:ring-2 focus:ring-indigo-300 focus:outline-none transition-all duration-300 
                              hover:shadow-sm text-gray-700 text-sm" />
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </span>
            </div>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @keydown.escape="open = false" type="button" 
                        class="flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm text-gray-700 rounded-xl 
                               shadow-sm hover:bg-indigo-50 hover:shadow-md transition-all duration-300 min-w-[120px] text-sm">
                    <span x-text="selectedFilter.charAt(0).toUpperCase() + selectedFilter.slice(1)"></span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" 
                     x-transition:leave-end="opacity-0 scale-95" @click.away="open = false" 
                     class="absolute z-10 mt-2 w-full bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-gray-100 py-1">
                    <template x-for="section in ['all', 'CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA']" :key="section">
                        <button @click="selectedFilter = section; open = false" type="button" 
                                class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-50 
                                       transition-colors duration-200 text-sm" 
                                :class="{ 'bg-indigo-100 text-indigo-800': selectedFilter === section }">
                            <span x-text="section.charAt(0).toUpperCase() + section.slice(1)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50/80 backdrop-blur-sm text-green-700 rounded-xl border border-green-100 
                    scroll-reveal shadow-sm transition-all duration-300 hover:shadow-md" 
             x-data="{ visible: false }" x-intersect="visible = true" :class="{ 'visible': visible }">
            {{ session('success') }}
        </div>
    @endif
    <div id="books-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($books as $book)
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden 
                        transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 
                        card-hover scroll-reveal" 
                 x-data="{ visible: false }" 
                 x-intersect="visible = true" 
                 :class="{ 'visible': visible }" 
                 x-show="selectedFilter === 'all' || selectedFilter === '{{ $book->section }}'">
                <div class="relative h-32 sm:h-40 md:h-48 bg-gray-50">
                    @if($book->image1)
                        <img src="{{ asset('storage/' . $book->image1) }}" alt="{{ $book->name }}" 
                             class="w-full h-full object-cover rounded-t-xl transition-transform duration-300 
                                    hover:scale-105" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-500 
                                    rounded-t-xl">
                            No Image
                        </div>
                    @endif
                    <span class="absolute top-2 sm:top-3 right-2 sm:right-3 text-xs font-semibold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm
                                 transform transition-all duration-200 hover:scale-105
                                 @if($book->section === 'CICS') bg-violet-300 text-violet-900
                                 @elseif($book->section === 'CTED') bg-sky-300 text-sky-900
                                 @elseif($book->section === 'CCJE') bg-red-300 text-red-900
                                 @elseif($book->section === 'CHM') bg-pink-300 text-pink-900
                                 @elseif($book->section === 'CBEA') bg-yellow-300 text-yellow-900
                                 @elseif($book->section === 'CA') bg-green-300 text-green-900
                                 @else bg-gray-100 text-gray-800 @endif">
                        {{ $book->section }}
                    </span>
                    @php
                        $borrowedBy = $book->borrowedBy();
                    @endphp
                    @if($borrowedBy)
                        @if($borrowedBy->status === 'approved' && $borrowedBy->student)
                            <span class="absolute top-2 sm:top-3 left-2 sm:left-3 bg-red-100 text-red-800 text-xs font-semibold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm transform transition-all duration-200 hover:scale-105">
                                Borrowed by {{ $borrowedBy->student->fname }} {{ $borrowedBy->student->lname }}
                            </span>
                        @elseif($borrowedBy->status === 'approved' && $borrowedBy->teacherVisitor)
                            <span class="absolute top-2 sm:top-3 left-2 sm:left-3 bg-red-100 text-red-800 text-xs font-semibold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm transform transition-all duration-200 hover:scale-105">
                                Borrowed by {{ $borrowedBy->teacherVisitor->fname }} {{ $borrowedBy->teacherVisitor->lname }}
                            </span>
                        @elseif($borrowedBy->status === 'pending')
                            <span class="absolute top-2 sm:top-3 left-2 sm:left-3 bg-yellow-100 text-yellow-800 text-xs font-semibold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm transform transition-all duration-200 hover:scale-105">
                                Waiting for approval
                            </span>
                        @else
                            <span class="absolute top-2 sm:top-3 left-2 sm:left-3 bg-gray-100 text-gray-800 text-xs font-semibold px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full shadow-sm transform transition-all duration-200 hover:scale-105">
                                Status: {{ ucfirst($borrowedBy->status) }}
                            </span>
                        @endif
                    @endif
                </div>
                <div class="p-3 sm:p-4 md:p-5 space-y-1 sm:space-y-2">
                    <p class="text-xs sm:text-sm text-gray-600">{{ $book->book_code }}</p>
                    <h3 class="text-sm sm:text-base md:text-lg font-semibold text-gray-800 line-clamp-2">{{ $book->name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500">by {{ $book->author }}</p>
                    <div class="flex flex-col space-y-2 pt-1 sm:pt-2">
                       <a href="{{ route('user.books.show', $book->id) }}"
                          class="w-full px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50/80 backdrop-blur-sm
                                 rounded-lg shadow-sm hover:bg-indigo-50 hover:shadow-md transition-all duration-200 text-center">
                           View
                       </a>
                       @if(!$book->isBorrowed())
                           <button @click="showReserveModal = true; reserveBookId = {{ $book->id }}"
                                   class="w-full px-3 py-2 text-sm font-medium text-white bg-indigo-600
                                          rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all duration-200">
                               Reserve
                           </button>
                       @else
                           <span class="w-full px-3 py-2 text-sm font-medium text-red-600 bg-red-100
                                  rounded-lg shadow-sm text-center">
                               Unavailable
                           </span>
                       @endif
                   </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="books-pagination" class="mt-8 flex justify-center">
        {{ $books->links('vendor.pagination.modern-gray') }}
    </div>
</div>
        <div id="ebook-container" style="display: none;">
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="AI">AI</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Agriculture">Agriculture</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Fiction">Fiction</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Science">Science</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="History">History</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Biography">Biography</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Technology">Technology</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Art">Art</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Business">Business</button>
                <button type="button" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm hover:bg-indigo-100 transition-colors category-btn" data-category="Health">Health</button>

            </div>
            <div class="row mb-3">
                <div class="col-md-8 mx-auto">
                    <form id="ebook-search-form" class="relative w-full md:w-1/2">
                        <input 
                            type="text" 
                            id="ebook-search-input" 
                            class="w-full p-3 pl-4 pr-12 rounded-lg border border-gray-300 bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 placeholder-gray-500" 
                            placeholder="Search for e-books..." 
                            value=""
                            required
                        >
                        <button 
                            type="submit" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 transition-colors duration-300"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <div id="ebook-results" class="flex flex-wrap justify-center">
                <!-- E-Book cards will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Reserve Confirmation Modal -->
    <div x-show="showReserveModal" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.away="showReserveModal = false; reserveBookId = null">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Reservation</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to reserve this book?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showReserveModal = false; reserveBookId = null"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <form x-show="reserveBookId" :action="'/user/books/' + reserveBookId + '/reserve'" method="POST" class="inline">
                    @csrf
                    <button type="submit" @click="showReserveModal = false"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        Confirm Reserve
                    </button>
                </form>
            </div>
        </div>
    </div>

    <x-footer />
    <script>
        // --- Library Book / E-Book Toggle and Google Books API Search ---
        document.addEventListener('DOMContentLoaded', function () {
            const libraryBtn = document.getElementById('library-books-btn');
            const ebookBtn = document.getElementById('ebook-btn');
            const libraryContainer = document.getElementById('library-books-container');
            const ebookContainer = document.getElementById('ebook-container');
            const ebookSearchForm = document.getElementById('ebook-search-form');
            const ebookSearchInput = document.getElementById('ebook-search-input');
            const ebookResults = document.getElementById('ebook-results');
            const grid = document.getElementById('books-grid');
            const noBooks = document.getElementById('no-books-placeholder');
            const pagination = document.getElementById('books-pagination');
            let ebookTabLoaded = false;

            function setActiveButton(activeBtn, inactiveBtn) {
                activeBtn.classList.add('bg-white', 'text-gray-700', 'shadow', 'active');
                activeBtn.classList.remove('bg-transparent', 'text-gray-500');
                inactiveBtn.classList.remove('bg-white', 'text-gray-700', 'shadow', 'active');
                inactiveBtn.classList.add('bg-transparent', 'text-gray-500');
            }

            if (libraryBtn && ebookBtn && libraryContainer && ebookContainer) {
                libraryBtn.addEventListener('click', function () {
                    setActiveButton(libraryBtn, ebookBtn);
                    libraryContainer.style.display = 'block';
                    ebookContainer.style.display = 'none';
                });
                ebookBtn.addEventListener('click', function () {
                    setActiveButton(ebookBtn, libraryBtn);
                    libraryContainer.style.display = 'none';
                    ebookContainer.style.display = 'block';
                    if (!ebookTabLoaded) {
                        // Auto-search a default term on first open
                        ebookTabLoaded = true;
                        ebookSearchInput.value = 'Library';
                        ebookSearchForm.dispatchEvent(new Event('submit'));
                    }
                });
            }

            // --- Category Buttons Logic ---
            const categoryBtns = document.querySelectorAll('.category-btn');
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Remove highlight from all
                    categoryBtns.forEach(b => b.classList.remove('bg-indigo-500', 'text-white'));
                    // Highlight this one
                    btn.classList.add('bg-indigo-500', 'text-white');
                    // Set search input and trigger search
                    ebookSearchInput.value = btn.dataset.category;
                    ebookSearchForm.dispatchEvent(new Event('submit'));
                });
            });

            // --- End Category Buttons Logic ---

            if (ebookSearchForm && ebookSearchInput && ebookResults) {
                ebookSearchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const query = ebookSearchInput.value.trim();
                    if (!query) return;
                    ebookResults.innerHTML = '<div class="text-center w-full py-8 text-gray-400">Searching...</div>';
                    fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=12`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.items || data.items.length === 0) {
                                ebookResults.innerHTML = '<div class="text-center w-full py-8 text-gray-400">No results found.</div>';
                                return;
                            }
                            // Use grid layout for uniform cards
                            ebookResults.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6';
                            ebookResults.innerHTML = '';
                            data.items.forEach(item => {
                                const info = item.volumeInfo;
                                const title = info.title || 'No Title';
                                const authors = info.authors ? info.authors.join(', ') : 'Unknown Author';
                                const description = info.description ? info.description : 'No description available.';
                                // Use the highest quality image available
                                let thumbnail = '';
                                if (info.imageLinks) {
                                    thumbnail = info.imageLinks.large || info.imageLinks.medium || info.imageLinks.small || info.imageLinks.thumbnail || 'https://via.placeholder.com/200x300?text=No+Cover';
                                } else {
                                    thumbnail = 'https://via.placeholder.com/200x300?text=No+Cover';
                                }
                                const link = info.infoLink || '#';
                                const card = document.createElement('div');
                                card.className = 'relative flex flex-col bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-2xl hover:scale-105 transition-all duration-300 h-96 overflow-hidden group';
                                card.innerHTML = `
                                    <div class="relative w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden group/image">
                                        <img src="${thumbnail}" class="object-contain w-full h-full transition-transform duration-300 group-hover/image:scale-105" alt="${title}">
                                        <div class="absolute inset-0 bg-black bg-opacity-70 text-white opacity-0 group-hover/image:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center p-4 text-center cursor-pointer">
                                            <div class="text-sm max-h-32 overflow-y-auto">${description}</div>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex flex-col p-4">
                                        <h6 class="font-semibold text-base text-gray-800 mb-1 line-clamp-2">${title}</h6>
                                        <p class="text-sm text-gray-500 mb-4 line-clamp-1">${authors}</p>
                                        <a href="${link}" target="_blank" rel="noopener" class="mt-auto inline-block px-4 py-2 rounded-lg bg-indigo-500 text-white text-xs font-semibold shadow hover:bg-indigo-600 transition-colors text-center">View</a>
                                    </div>
                                `;
                                ebookResults.appendChild(card);
                            });
                        })
                        .catch(() => {
                            ebookResults.innerHTML = '<div class="text-center w-full py-8 text-red-400">Error fetching results.</div>';
                        });
                });
            }
        });
        // --- End Google Books API Search ---

        // --- Library Book Search Filtering ---
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('library-search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = searchInput.value.trim().toLowerCase();
                    document.querySelectorAll('#library-books-container .grid > div').forEach(card => {
                        // Find title, author, and book code text
                        const title = card.querySelector('h3')?.textContent?.toLowerCase() || '';
                        const author = card.querySelector('p.text-sm.text-gray-600')?.textContent?.toLowerCase() || '';
                        const code = card.querySelector('p.text-md')?.textContent?.toLowerCase() || '';
                        // Section filter (from Alpine)
                        const section = card.querySelector('span.absolute.top-2.right-2')?.textContent?.trim() || '';
                        // Get selectedFilter from Alpine
                        let selectedFilter = 'all';
                        try {
                            selectedFilter = document.body.__x.$data.selectedFilter;
                        } catch {}
                        // Show/hide based on search and filter
                        const matchesSearch = !query || title.includes(query) || author.includes(query) || code.includes(query);
                        const matchesSection = selectedFilter === 'all' || section === selectedFilter;
                        card.style.display = (matchesSearch && matchesSection) ? '' : 'none';
                    });
                    // After filtering, update placeholder
                    updateNoBooksPlaceholder();
                });
                // Initial check
                updateNoBooksPlaceholder();
                // Also re-filter when section changes (Alpine)
                // Also update when the section dropdown is used (delegate clicks)
                document.addEventListener('click', (e) => {
                    const btn = e.target.closest('button');
                    if (!btn) return;
                    // Allow Alpine to toggle x-show first
                    setTimeout(() => updateNoBooksPlaceholder(), 0);
                });
            }
        });
        // --- End Library Book Search Filtering ---

        // Initialize Intersection Observer for scroll-reveal animations
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.scroll-reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>