<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin | Books</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Chart.js (included for consistency, though not used in this page) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Vite Scripts -->
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
            <!-- Sidebar Navigation -->
            <nav class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-100 transition-all duration-300 ease-in-out"
                :class="{'w-16': !sidebarExpanded, 'w-60': sidebarExpanded}">
                
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 border-b dark:border-gray-300">
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
                        <img src="/images/library.png" alt="" width="30" height="30">           
                        <span x-show="sidebarExpanded" class="ml-2 text-gray-800 font-semibold transition-opacity duration-300" 
                              x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            Library
                        </span>
                    </a>
                </div>

                <!-- Main Navigation -->
                <div class="mt-4 grid">
                    <!-- Dashboard Link -->
                    <x-nav-link :href="route('admin.auth.dashboard')" :active="request()->routeIs('admin.auth.dashboard')" class="flex items-center px-5 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                              x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            Dashboard
                        </span>
                    </x-nav-link>
                    <!-- Students Link -->
                    <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.index')" class="flex items-center px-4 py-3">
                        <img src="/images/study.png" alt="" width="25" height="25">
                        <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                              x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            Students
                        </span>
                    </x-nav-link>
                    <!-- Attendance Link -->
                <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Attendance
                    </span>
                </x-nav-link>
                    <!-- Books Link -->
                    <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.index')" class="flex items-center px-5 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                        </svg>
                        <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                              x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            Books
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.borrow.requests')" :active="request()->routeIs('admin.borrow.requests')" class="flex items-center px-5 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray"><path d="M4 4h12v2H4V4zm0 4h12v2H4V8zm0 4h8v2H4v-2z"/></svg>
                        <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Borrow Requests</span>
                    </x-nav-link>
                </div>

                <!-- User Profile Section (Bottom) -->
                <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200 p-4 bg-white dark:bg-gray-100">
                    <div class="relative">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button class="flex items-center w-full text-left focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-200 rounded-md transition-colors duration-200 p-1">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 rounded-full bg-gray-200 p-1 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div x-show="sidebarExpanded" class="ml-3 overflow-hidden transition-all duration-300 ease-in-out" 
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                    </div>
                                    <div x-show="sidebarExpanded" class="ml-auto transition-transform duration-200 ease-in-out" 
                                        :class="{ 'rotate-180': open }">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="fixed flex flex-row items-center justify-center rounded-lg bg-gray-400 shadow-lg z-50 overflow-hidden transition-all duration-300 ease-out w-[18.25rem] h-[10vh] absolute top-[-65px]"
                                    :style="sidebarExpanded ? 'left: 15.5em' : 'left: 7rem'"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95">
                                    <div class="flex flex-row items-center space-x-4 px-4 py-2 bg-white dark:bg-white rounded-lg shadow-full border border-gray-200">
                                        <!-- Profile Link -->
                                        <x-dropdown-link :href="route('profile.edit')"
                                                         class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-300 px-3 py-2 rounded transition-colors duration-200">
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0H4.5z" />
                                            </svg>
                                            <span class="text-gray-800">{{ __('Profile') }}</span>
                                        </x-dropdown-link>
                                        <!-- Vertical Divider -->
                                        <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                                        <!-- Logout Form + Link -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')"
                                                             onclick="event.preventDefault(); this.closest('form').submit();"
                                                             class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-300 px-3 py-2 rounded transition-colors duration-200">
                                                <svg class="w-8 h-8 text-gray-500 dark:text-gray-300" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3h-8.25m0 0l3-3m-3 3l3 3" />
                                                </svg>
                                                <span class="text-gray-800">{{ __('Log Out') }}</span>
                                            </x-dropdown-link>
                                        </form>
                                    </div>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                <!-- Toggle Button -->
                <button @click="sidebarExpanded = !sidebarExpanded" 
                        class="absolute -right-3 top-6 bg-white dark:bg-white border dark:border-gray-300 rounded-full p-1 shadow-md hover:bg-gray-100 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path x-show="!sidebarExpanded" fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        <path x-show="sidebarExpanded" fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </nav>

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
