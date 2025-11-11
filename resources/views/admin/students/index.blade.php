<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Students</title>
        <link rel="icon" type="image/png" href="/favicon/library.png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite([
            'resources/css/app.css',
            'resources/css/admin/students-page.css',
            'resources/js/app.js',
            'resources/js/admin/students-page.js'
        ])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-100">

        <div id="main-content" class="transition-all duration-500 ml-64 main-content min-h-screen bg-gray-100 dark:bg-gray-100 flex">
            <x-admin-nav-bar />

            <!-- Content Area -->
            <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
                <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-center border border-gray-200 shadow-sm py-8 bg-white rounded-lg max-w-9xl mx-auto space-y-6 mt-6">
                        <div class="w-full max-w-7xl space-y-6">
                            @php
                            $collegeCounts = [
                                ['label' => 'Total Students', 'count' => $students->count(), 'desc' => '+' . $students->count(), 'descColor' => 'text-green-500'],
                                ['label' => 'CICS Students', 'count' => $students->where('college', 'CICS')->count(), 'desc' => 'College of Information and Computing Sciences', 'descColor' => 'text-violet-500'],
                                ['label' => 'CTED Students', 'count' => $students->where('college', 'CTED')->count(), 'desc' => 'College of Teacher Education', 'descColor' => 'text-blue-500'],
                                ['label' => 'CCJE Students', 'count' => $students->where('college', 'CCJE')->count(), 'desc' => 'College of Criminal Justice', 'descColor' => 'text-red-500'],
                                ['label' => 'CHM Students', 'count' => $students->where('college', 'CHM')->count(), 'desc' => 'College of Hospitality Management', 'descColor' => 'text-pink-300'],
                                ['label' => 'CBEA Students', 'count' => $students->where('college', 'CBEA')->count(), 'desc' => 'College of Business Education and Accountancy', 'descColor' => 'text-yellow-500'],
                                ['label' => 'CA Students', 'count' => $students->where('college', 'CA')->count(), 'desc' => 'College of Agriculture', 'descColor' => 'text-green-400'],
                            ];
                            @endphp

                            <!-- Statistic Cards -->
                            <div x-data="{ showMore: false }" class="space-y-4 mb-8">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                    @foreach ($collegeCounts as $index => $college)
                                        @if ($index < 4)
                                            <div class="bg-white shadow-sm rounded-xl p-6 border">
                                                <h3 class="text-sm text-gray-500">{{ $college['label'] }}</h3>
                                                <p class="text-2xl font-semibold text-gray-800 mt-2">{{ $college['count'] }}</p>
                                                <span class="text-xs {{ $college['descColor'] }} mt-1 block">{{ $college['desc'] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Collapsible Additional Colleges -->
                                <div x-show="showMore" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                    @foreach ($collegeCounts as $index => $college)
                                        @if ($index >= 4)
                                            <div class="bg-white shadow-sm rounded-xl p-6 border">
                                                <h3 class="text-sm text-gray-500">{{ $college['label'] }}</h3>
                                                <p class="text-2xl font-semibold text-gray-800 mt-2">{{ $college['count'] }}</p>
                                                <span class="text-xs {{ $college['descColor'] }} mt-1 block">{{ $college['desc'] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Toggle Button -->
                                <div class="text-center">
                                    <button @click="showMore = !showMore" class="inline-flex items-center gap-1 text-sm text-gray-700 hover:text-gray-900 transition font-medium">
                                        <span x-show="!showMore">Show More</span>
                                        <span x-show="showMore">Show Less</span>
                                        <svg x-show="!showMore" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                             viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <svg x-show="showMore" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                             viewBox="0 0 24 24">
                                            <path d="M5 15l7-7 7 7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>


                            <!-- Table Card -->
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow">

                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-6 bg-gray-50 border-b border-gray-200 gap-6">
                                    <!-- Title -->
                                    <div>
                                        <h2 id="students-table-title" class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                                            Active Students
                                        </h2>
                                    </div>

                                    <!-- Filters Section -->
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 w-full md:w-auto">
                                        <div class="flex flex-col gap-2">
                                            <h3 class="text-sm font-medium text-gray-600">Filter Students</h3>
                                            <div class="flex flex-wrap gap-2">
                                                <!-- College Filter -->
                                                <div class="relative">
                                                    <button id="collegeFilterButton"
                                                        class="flex items-center justify-between w-36 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 transition">
                                                        <span id="selectedCollege">Colleges</span>
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <ul id="collegeFilterMenu"
                                                        class="absolute z-50 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md hidden transform origin-top transition duration-150 ease-out">
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="All">All</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CICS">CICS</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CTED">CTED</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CCJE">CCJE</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CHM">CHM</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CBEA">CBEA</button></li>
                                                        <li><button class="college-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-college="CA">CA</button></li>
                                                    </ul>
                                                </div>

                                                <!-- Year Filter -->
                                                <div class="relative">
                                                    <button id="yearFilterButton"
                                                        class="flex items-center justify-between w-36 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 transition">
                                                        <span id="selectedYear">Year</span>
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <ul id="yearFilterMenu"
                                                        class="absolute z-50 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md hidden transform origin-top transition duration-150 ease-out">
                                                        <li><button class="year-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-year="All">All</button></li>
                                                        <li><button class="year-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-year="1">1st Year</button></li>
                                                        <li><button class="year-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-year="2">2nd Year</button></li>
                                                        <li><button class="year-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-year="3">3rd Year</button></li>
                                                        <li><button class="year-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-year="4">4th Year</button></li>
                                                    </ul>
                                                </div>

                                                <!-- Gender Filter -->
                                                <div class="relative">
                                                    <button id="genderFilterButton"
                                                        class="flex items-center justify-between w-36 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 transition">
                                                        <span id="selectedGender">Gender</span>
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <ul id="genderFilterMenu"
                                                        class="absolute z-50 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md hidden transform origin-top transition duration-150 ease-out">
                                                        <li><button class="gender-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-gender="All">All</button></li>
                                                        <li><button class="gender-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-gender="Male">Male</button></li>
                                                        <li><button class="gender-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-gender="Female">Female</button></li>
                                                        <li><button class="gender-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-gender="Prefer not to say">Prefer not to say</button></li>
                                                        <li><button class="gender-filter-option w-full px-4 py-2 text-left text-sm hover:bg-gray-50" data-gender="Other">Other</button></li>
                                                    </ul>
                                                </div>

                                                <!-- Print Button -->
                                                <button id="print-selected-btn"
                                                    class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 transition">
                                                    Print QR Codes
                                                </button>

                                                <!-- Archive Selected Button -->
                                                <button id="archive-selected-btn"
                                                    class="inline-flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-2 text-sm font-medium hover:bg-red-100 focus:ring-2 focus:ring-red-500 transition hidden">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                    </svg>
                                                    Archive Selected
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-wrap items-center gap-3 md:ml-6">
                                            <button id="toggle-archived-view"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                            <span class="button-text">View Archived</span>
                                            </button>

                                            <a href="{{ route('admin.students.create')}}"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                                
                                            + Add Student
                                            </a>
                                            <a href="{{ route('admin.students.bulk-create')}}"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                           </svg>
                                                Bulk Upload
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Search Bar -->
                                <div class="bg-white p-1">
                                    <div class="relative max-w-md">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" id="student-search" placeholder="Search students by name..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>

                                <!-- Active Students Table -->
                                <div id="active-students-section">
                                    <div class="overflow-x-auto p-4 py-6">
                                        <table class="w-full table-auto text-sm text-left text-gray-700">
                                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b">
                                                <tr>
                                                    <th class="px-2 py-3">
                                                        <label for="select-all" class="inline-flex items-center gap-2">
                                                            <input type="checkbox" id="select-all" class="h-4 w-4">
                                                            <span>Select All</span>
                                                        </label>
                                                    </th>
                                                    <th class="px-6 py-3">Student ID</th>
                                                    <th class="px-6 py-3">Last Name</th>
                                                    <th class="px-6 py-3">First Name</th>
                                                    <th class="px-3 py-3">MI</th>
                                                    <th class="px-6 py-3">College</th>
                                                    <th class="px-3 py-3">Year</th>
                                                    <th class="px-6 py-3">Gender</th>
                                                    <th class="px-2 py-3">Email</th>
                                                    <th class="px-2 py-3">QR Code</th>
                                                    <th class="px-2 py-3 text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100" id="student-table-body">
                                                @foreach ($students as $student)
                                                <tr class="hover:bg-gray-50" data-college="{{ $student->college }}" data-gender="{{ $student->gender ?? 'N/A' }}">
                                                    <td class="px-2 py-4"><input type="checkbox" class="select-student" value="{{ $student->id }}" data-name="{{ $student->lname }}, {{ $student->fname }}{{ $student->MI ? ' ' . $student->MI . '.' : '' }}" data-student-id="{{ $student->student_id }}" data-college="{{ $student->college }}" data-year="{{ $student->year }}" data-qr="{{ $student->qr_code_path ? asset('storage/' . $student->qr_code_path) : '' }}"></td>
                                                    <td class="px-6 py-4">{{ $student->student_id }}</td>
                                                    <td class="px-6 py-4">{{ $student->lname }}</td>
                                                    <td class="px-6 py-4">{{ $student->fname }}</td>
                                                    <td class="px-3 py-4">{{ $student->MI }}</td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-2 py-1 text-xs font-medium rounded-md
                                                            @if($student->college === 'CICS') bg-violet-200 text-gray-800
                                                            @elseif($student->college === 'CTED') bg-sky-200 text-gray-800
                                                            @elseif($student->college === 'CCJE') bg-red-300 text-gray-800
                                                            @elseif($student->college === 'CHM') bg-pink-300 text-gray-800
                                                            @elseif($student->college === 'CBEA') bg-yellow-200 text-gray-800
                                                            @elseif($student->college === 'CA') bg-green-300 text-gray-800
                                                            @else bg-gray-100 text-gray-700 @endif">
                                                            {{ $student->college }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4">{{ $student->year }}</td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-2 py-1 text-xs font-medium rounded-md
                                                            @if($student->gender === 'Male') bg-blue-200 text-gray-800
                                                            @elseif($student->gender === 'Female') bg-pink-200 text-gray-800
                                                            @else bg-gray-100 text-gray-700 @endif">
                                                            {{ $student->gender ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-2 py-4 text-gray-600 hover:underline">
                                                        <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        @if($student->qr_code_path)
                                                            <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="QR Code" class="qr-thumb w-16 h-16 object-contain border rounded" data-name="{{ $student->fname }} {{ $student->lname }}" />
                                                        @else
                                                            <span class="text-xs text-gray-400">No QR</span>
                                                        @endif
                                                    </td>

                                                    <td class="px-2 py-4 text-right">
                                                        <!-- Edit Button -->
                                                        <button type="button"
                                                                class="edit-student-btn inline-flex items-center px-3 py-1 bg-sky-50 border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-sky-100 transition-colors"
                                                                title="Edit" aria-label="Edit student {{ $student->student_id }}"
                                                                data-student-id="{{ $student->id }}"
                                                                data-student-student-id="{{ $student->student_id }}"
                                                                data-lname="{{ $student->lname }}"
                                                                data-fname="{{ $student->fname }}"
                                                                data-mi="{{ $student->MI }}"
                                                                data-gender="{{ $student->gender }}"
                                                                data-email="{{ $student->email }}"
                                                                data-college="{{ $student->college }}"
                                                                data-year="{{ $student->year }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.7 2.7 0 0 1 3.818 3.818L7.227 20.758a4.5 4.5 0 0 1-1.897 1.13l-3.278.984.984-3.278a4.5 4.5 0 0 1 1.13-1.897L16.862 3.487z"/>
                                                            </svg>
                                                        </button>
                                                        <!-- Resend QR Button (POST) -->
                                                        <form title="Resend QR Code" action="{{ route('admin.students.resend-qr', $student->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="resend-qr-btn inline-flex items-center px-3 py-1 bg-green-50 border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-green-100 transition-colors"
                                                                aria-label="Resend QR to {{ $student->email }}">
                                                                <span class="spinner hidden w-4 h-4 border-2 border-gray-700 border-t-transparent rounded-full animate-spin mr-1"></span>
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <!-- Archive Button -->
                                                        <form action="{{ route('admin.students.archive', $student->id) }}" method="POST" class="inline archive-form" data-student-name="{{ $student->fname }} {{ $student->lname }}">
                                                            @csrf
                                                            <button type="button"
                                                                class="archive-btn inline-flex items-center px-3 py-1 bg-red-50 border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-red-100 transition-colors"
                                                                title="Archive" aria-label="Archive student {{ $student->student_id }}">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125 1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($students->isEmpty())
                                    <div class="p-8 text-center text-gray-500 h-[22vh] flex justify-center items-center">
                                        No active students found. Add your first student!
                                    </div>
                                    @endif
                                </div>

                                <!-- Archived Students Section -->
                                <div id="archived-students-section" style="display: none;">
                                    <div class="overflow-x-auto p-4 py-6">
                                        <table class="w-full table-auto text-sm text-left text-gray-700">
                                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b">
                                                <tr>
                                                    <th class="px-6 py-3">Student ID</th>
                                                    <th class="px-6 py-3">Last Name</th>
                                                    <th class="px-6 py-3">First Name</th>
                                                    <th class="px-3 py-3">MI</th>
                                                    <th class="px-6 py-3">College</th>
                                                    <th class="px-3 py-3">Year</th>
                                                    <th class="px-6 py-3">Gender</th>
                                                    <th class="px-2 py-3">Email</th>
                                                    <th class="px-2 py-3">Archived Date</th>
                                                    <th class="px-2 py-3 text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @forelse($archivedStudents as $student)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4">{{ $student->student_id }}</td>
                                                    <td class="px-6 py-4">{{ $student->lname }}</td>
                                                    <td class="px-6 py-4">{{ $student->fname }}</td>
                                                    <td class="px-3 py-4">{{ $student->MI }}</td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-2 py-1 text-xs font-medium rounded-md
                                                            @if($student->college === 'CICS') bg-violet-200 text-gray-800
                                                            @elseif($student->college === 'CTED') bg-sky-200 text-gray-800
                                                            @elseif($student->college === 'CCJE') bg-red-300 text-gray-800
                                                            @elseif($student->college === 'CHM') bg-pink-300 text-gray-800
                                                            @elseif($student->college === 'CBEA') bg-yellow-200 text-gray-800
                                                            @elseif($student->college === 'CA') bg-green-300 text-gray-800
                                                            @else bg-gray-100 text-gray-700 @endif">
                                                            {{ $student->college }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4">{{ $student->year }}</td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-2 py-1 text-xs font-medium rounded-md
                                                            @if($student->gender === 'Male') bg-blue-200 text-gray-800
                                                            @elseif($student->gender === 'Female') bg-pink-200 text-gray-800
                                                            @else bg-gray-100 text-gray-700 @endif">
                                                            {{ $student->gender ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-2 py-4 text-gray-600 hover:underline">
                                                        <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                                    </td>
                                                    <td class="px-2 py-4 text-sm text-gray-500">
                                                        {{ $student->archived_at ? $student->archived_at->format('M d, Y') : 'N/A' }}
                                                    </td>
                                                    <td class="px-2 py-4 text-right">
                                                        <!-- Unarchive Button -->
                                                        <form action="{{ route('admin.students.unarchive', $student->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1 bg-green-50 border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-green-100 transition-colors"
                                                                title="Unarchive" aria-label="Unarchive student {{ $student->student_id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <!-- Permanent Delete Button -->
                                                        <form action="{{ route('admin.students.permanent-delete', $student->id) }}" method="POST" class="inline delete-form" data-student-name="{{ $student->fname }} {{ $student->lname }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="delete-btn inline-flex items-center px-3 py-1 bg-red-50 border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-red-100 transition-colors"
                                                                title="Permanently Delete" aria-label="Permanently delete student {{ $student->student_id }}">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                        </svg>
                                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No archived students</h3>
                                                        <p class="mt-1 text-sm text-gray-500">Get started by archiving a student from the active students list.</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </main>

            </div>
        </div>

        <!-- Page Data for JS Module -->
        <div id="students-page-data"
             data-college-labels='@json(array_column($collegeCounts, "label"))'
             data-college-counts='@json(array_column($collegeCounts, "count"))'
             data-year-counts='[{{ $students->where("year", 1)->count() }},{{ $students->where("year", 2)->count() }},{{ $students->where("year", 3)->count() }},{{ $students->where("year", 4)->count() }}]'></div>
        <div id="flash-data" data-success='@json(session("success"))' data-error='@json(session("error"))'></div>

        <!-- Batch Print Modal -->
        <style>
            @media print {
                /* Hard reset layout to avoid a blank first page */
                html, body { margin: 0 !important; padding: 0 !important; }
                /* Only print the batch print modal (remove others from flow entirely) */
                body * { display: none !important; }
                #batch-print-modal.print-mode { display: block !important; }
                /* Make all descendants visible again after body* was hidden */
                #batch-print-modal.print-mode * { display: initial !important; }
                /* Ensure grid container keeps its grid layout in print */
                #batch-print-modal.print-mode #batch-print-grid { display: grid !important; }

                /* Normalize layout to full page and remove background overlay */
                #batch-print-modal.print-mode { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #ffffff !important; opacity: 1 !important; overflow: visible !important; }
                /* Inner container: remove extra spacing and add predictable print padding */
                #batch-print-modal.print-mode > div { box-shadow: none !important; max-width: 100% !important; width: 100% !important; max-height: none !important; border-radius: 0 !important; padding: 0.35in 0.35in 0.4in !important; margin: 0 !important; overflow: visible !important; }

                /* Hide title, close button and footer in print */
                #batch-print-modal.print-mode h2,
                #batch-print-modal.print-mode #close-batch-print,
                #batch-print-modal.print-mode .border-t { display: none !important; }

                /* Grid and blocks: 3x3 per page */
                #batch-print-grid { gap: 0.25in !important; padding: 0 !important; overflow: visible !important; max-height: none !important; grid-template-columns: repeat(3, 1fr) !important; }
                .qr-block { break-inside: avoid; page-break-inside: avoid; width: 100% !important; height: 3.0in !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: flex-start !important; padding: 0.1in !important; }
                /* Ensure labels are on separate lines (increase specificity) */
                #batch-print-modal.print-mode .qr-block .name { display: block !important; width: 100% !important; font-size: 12pt !important; margin-bottom: 0.05in !important; text-align: center !important; white-space: normal !important; }
                #batch-print-modal.print-mode .qr-block .college { display: block !important; width: 100% !important; font-size: 10pt !important; margin-bottom: 0.08in !important; text-align: center !important; white-space: normal !important; }
                /* Images scaled to fit 3 rows */
                .qr-block img { width: 2.2in !important; height: 2.2in !important; object-fit: contain !important; }

                /* Hide scrollbars in print-capable browsers */
                #batch-print-grid::-webkit-scrollbar { display: none !important; }
            }
            @page { margin: 0.35in; }
        </style>
        <div id="batch-print-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl w-full max-h-[90vh] relative animate-fadeIn flex flex-col">
                <button id="close-batch-print" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold z-10">&times;</button>
                <h2 class="text-xl font-semibold text-center mb-6">Batch Print QR Codes</h2>
                <div id="batch-print-grid" class="grid grid-cols-3 gap-6 justify-items-center overflow-y-auto flex-1"></div>
                <div class="flex justify-center mt-6 border-t pt-4 bg-white">
                    <button id="modal-print-btn" class="px-6 py-2 bg-green-600 text-white rounded-md font-medium hover:bg-green-700 transition">Print</button>
                </div>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div id="qr-code-modal" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-70 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">
            <div class="relative bg-white rounded-lg p-6 max-w-md w-full shadow-xl transform scale-95 transition-transform duration-300 ease-in-out flex justify-center items-center flex-col">
                <button id="close-qr-modal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold transition-transform hover:scale-110">&times;</button>
                <h3 id="qr-code-modal-name" class="text-lg font-semibold text-center mb-4"></h3>
                <img id="qr-code-modal-img" src="" alt="QR Code" class="max-w-full max-h-[80vh] object-contain rounded-md" />
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div id="edit-student-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Student</h3>
                <form id="edit-student-form" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @csrf
                    @method('PUT')

                    {{-- Student ID --}}
                    <div>
                        <label for="edit-student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                        <input type="text" id="edit-student_id" name="student_id" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label for="edit-lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="edit-lname" name="lname" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    {{-- First Name --}}
                    <div>
                        <label for="edit-fname" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="edit-fname" name="fname" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    {{-- Middle Initial --}}
                    <div>
                        <label for="edit-MI" class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                        <input type="text" id="edit-MI" name="MI"
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="edit-gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="edit-gender" name="gender"
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="" disabled>Choose Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="edit-email" name="email" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    {{-- College --}}
                    <div>
                        <label for="edit-college" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                        <select id="edit-college" name="college" required
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="" disabled>Choose College</option>
                            <option value="CICS">CICS</option>
                            <option value="CTED">CTED</option>
                            <option value="CCJE">CCJE</option>
                            <option value="CHM">CHM</option>
                            <option value="CBEA">CBEA</option>
                            <option value="CA">CA</option>
                        </select>
                    </div>

                    {{-- Year --}}
                    <div>
                        <label for="edit-year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <select id="edit-year" name="year" required
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="" disabled>Choose Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    {{-- Submit --}}
                    <div class="md:col-span-2 flex justify-end space-x-3 mt-6">
                        <button type="button" id="cancel-edit-student" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Update Student</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Archive Confirmation Modal -->
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Archive</h3>
                <p class="text-gray-600 mb-6" id="archive-modal-message">Are you sure you want to archive this student?</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancel-archive" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancel</button>
                    <button id="confirm-archive" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Archive</button>
                </div>
            </div>
        </div>

        <!-- Bulk Archive Confirmation Modal -->
        <div id="bulk-archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Bulk Archive</h3>
                <p class="text-gray-600 mb-6" id="bulk-archive-modal-message">Are you sure you want to archive the selected student(s)?</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancel-bulk-archive" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancel</button>
                    <button id="confirm-bulk-archive" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Archive Selected</button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Permanent Delete</h3>
                <p class="text-gray-600 mb-6" id="delete-modal-message">Are you sure you want to permanently delete this student? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancel-delete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancel</button>
                    <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Delete Permanently</button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast" class="fixed top-6 right-6 z-50 hidden px-6 py-3 rounded shadow-lg text-white text-base font-medium transition-all duration-300"></div>
    </body>
</html>
