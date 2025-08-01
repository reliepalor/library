<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin | Students</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Chart.js -->
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

            @keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
            .animate-fadeIn { animation: fadeIn 0.3s; }
            @media print {
                body * { visibility: hidden !important; }
                #batch-print-modal, #batch-print-modal * { visibility: visible !important; }
                #batch-print-modal { position: static !important; background: #fff !important; box-shadow: none !important; }
                #close-batch-print, #modal-print-btn { display: none !important; }
                .print-container, .content-area, .sidebar, nav, header, footer { display: none !important; }
            }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-100 flex">
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

                            <!-- Charts -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pie Chart: Distribution per College -->
                                <div class="bg-white rounded-xl shadow-sm p-6 border">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Students per College</h3>
                                    <div class="flex justify-center">
                                        <div style="width: 100%; max-width: 600px; height: 222px;">
                                            <canvas id="collegePieChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bar Chart: Year Level Breakdown -->
                                <div class="bg-white rounded-xl shadow-sm p-4 border h-[320px]">
                                    <h3 class="text-base font-semibold text-gray-800 mb-3">Students by Year Level</h3>
                                    <div class="h-[250px]">
                                        <canvas id="yearBarChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Card -->
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="flex flex-col md:flex-row justify-between items-center p-6 bg-gray-100 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">Student List</h2>
                                    <div class="flex flex-wrap items-center gap-2 mb-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="relative inline-block text-left">
                                                <button id="collegeFilterButton" class="college-filter glass-button px-4 py-2 text-gray-500 text-sm font-medium rounded-2xl flex items-center justify-between w-32 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/10 backdrop-blur-md border border-gray-400 shadow-md" data-college="All" aria-expanded="false" aria-controls="collegeFilterMenu">
                                                    <span id="selectedCollege">All</span>
                                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <ul id="collegeFilterMenu" class="absolute z-20 mt-2 w-32 bg-white/10 backdrop-blur-md border border-gray-400 rounded-2xl shadow-2xl hidden transform origin-top transition-all duration-300 ease-out opacity-0 scale-y-95">
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="All">All</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CICS">CICS</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CTED">CTED</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CCJE">CCJE</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CHM">CHM</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CBEA">CBEA</button>
                                                    </li>
                                                    <li>
                                                        <button class="college-filter-option w-full px-4 py-2 text-gray-500 text-sm font-medium text-left hover:bg-white/20 transition-all duration-200" data-college="CA">CA</button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <button id="applyFiltersButton" class="glass-button px-3 py-1.5 text-gray-500 text-sm font-medium rounded-2xl transition-all duration-300 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/10 backdrop-blur-md border border-gray-400 shadow-md hidden">
                                                Apply
                                            </button>
                                        
                                        </div>

                                        <button id="print-selected-btn" class="lass-button px-4 py-2 text-gray-500 text-sm font-medium rounded-2xl flex items-center justify-between w-32 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/10 backdrop-blur-md border border-gray-400 shadow-md">Print QR Code</button>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('admin.students.archived') }}" title="Archived Students"
                                           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                           
                                        </a>
                                        <a href="{{ route('admin.students.create')}}" 
                                           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition">
                                           + Add Student
                                        </a>
                                    </div>
                                </div>

                                

                                <div class="overflow-x-auto p-4">
                                    <table class="w-full table-auto text-sm text-left text-gray-700">
                                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b">
                                            <tr>
                                                <th class="px-2 py-3"><input type="checkbox" id="select-all"></th>
                                                <th class="px-6 py-3">Student ID</th>
                                                <th class="px-6 py-3">Last Name</th>
                                                <th class="px-6 py-3">First Name</th>
                                                <th class="px-6 py-3">MI</th>
                                                <th class="px-6 py-3">College</th>
                                                <th class="px-6 py-3">Year</th>
                                                <th class="px-2 py-3">Email</th>
                                                <th class="px-2 py-3">QR Code</th>
                                                <th class="px-2 py-3 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100" id="student-table-body">
                                            @foreach ($students as $student)
                                            <tr class="hover:bg-gray-50" data-college="{{ $student->college }}">
                                                <td class="px-2 py-4"><input type="checkbox" class="select-student" value="{{ $student->id }}" data-name="{{ $student->lname }}, {{ $student->fname }}{{ $student->MI ? ' ' . $student->MI . '.' : '' }}" data-studentid="{{ $student->student_id }}" data-qr="{{ $student->qr_code_path ? asset('storage/' . $student->qr_code_path) : '' }}"></td>
                                                <td class="px-6 py-4">{{ $student->student_id }}</td>
                                                <td class="px-6 py-4">{{ $student->lname }}</td>
                                                <td class="px-6 py-4">{{ $student->fname }}</td>
                                                <td class="px-6 py-4">{{ $student->MI }}</td>
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
                                                <td class="px-6 py-4">{{ $student->year }}</td>
                                                <td class="px-2 py-4 text-gray-600 hover:underline">
                                                    <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                                </td>
                                                <td class="px-4 py-4">
                                                    @if($student->qr_code_path)
                                                        <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="QR Code" class="w-16 h-16 object-contain border rounded" />
                                                    @else
                                                        <span class="text-xs text-gray-400">No QR</span>
                                                    @endif
                                                </td>
                                     
                                                <td class="px-2 py-4 text-right">
                                                    <!-- Edit Button -->
                                                    <a href="#" data-id="{{ $student->id }}"
                                                       class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-sm hover:bg-yellow-200 transition"
                                                       title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.7 2.7 0 0 1 3.818 3.818L7.227 20.758a4.5 4.5 0 0 1-1.897 1.13l-3.278.984.984-3.278a4.5 4.5 0 0 1 1.13-1.897L16.862 3.487z"/>
                                                        </svg>
                                                    </a>
                                                    <!-- Resend QR Button -->
                                                    <a href="{{ route('admin.students.resend-qr', $student->id) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-md text-sm hover:bg-blue-200 transition"
                                                       title="Resend QR">
                                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                                        </svg>
                                                    </a>
                                                    <!-- Archive Button -->
                                                    <form action="{{ route('admin.students.archive', $student->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-md text-sm hover:bg-gray-200 transition"
                                                            onclick="return confirm('Are you sure you want to archive this student?')"
                                                            title="Archive">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
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
                                <div class="p-8 text-center text-gray-500">
                                    No students found. Add your first student!
                                </div>
                                @endif

                     
                            </div>

                            
                        </div>
                    </div>
                    
                </main>
                
            </div>
        </div>

        <!-- Batch Print Modal -->
        <div id="batch-print-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl w-full relative animate-fadeIn">
                <button id="close-batch-print" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
                <h2 class="text-xl font-semibold text-center mb-6">Batch Print QR Codes</h2>
                <div id="batch-print-grid" class="grid grid-cols-3 grid-rows-2 gap-6 justify-items-center"></div>
                <div class="flex justify-center mt-6">
                    <button id="modal-print-btn" class="px-6 py-2 bg-green-600 text-white rounded-md font-medium hover:bg-green-700 transition">Print</button>
                </div>
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div id="edit-student-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl w-full relative animate-fadeIn">
                <button id="close-edit-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Student</h2>
                <div id="edit-modal-errors" class="mb-4 text-red-600 text-sm hidden"></div>
                <form id="edit-student-form" class="space-y-6" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="student_id_hidden" id="edit-student-id-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">Student ID</label>
                            <input type="text" name="student_id" id="edit-student-id" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">Last Name</label>
                            <input type="text" name="lname" id="edit-lname" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">First Name</label>
                            <input type="text" name="fname" id="edit-fname" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">MI</label>
                            <input type="text" name="MI" id="edit-mi" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">College</label>
                            <select name="college" id="edit-college" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                                <option value="" disabled>Choose College</option>
                                <option value="CICS">CICS</option>
                                <option value="CTED">CTED</option>
                                <option value="CCJE">CCJE</option>
                                <option value="CHM">CHM</option>
                                <option value="CBEA">CBEA</option>
                                <option value="CA">CA</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700">Year Level</label>
                            <input type="number" name="year" id="edit-year" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="edit-email" required class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast" class="fixed top-6 right-6 z-50 hidden px-6 py-3 rounded shadow-lg text-white text-base font-medium transition-all duration-300"></div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-confirmation-modal" class="fixed inset-0 z-[11000] flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-lg transform scale-95 transition-transform duration-300">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Confirm Delete</h3>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this student? This action cannot be undone.</p>
                <div class="flex justify-end space-x-4">
                    <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 transition">Cancel</button>
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Delete</button>
                </div>
                <button id="close-delete-modal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div id="qr-code-modal" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-70 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">
            <div class="relative bg-white rounded-lg p-6 max-w-md w-full shadow-xl transform scale-95 transition-transform duration-300 ease-in-out">
                <button id="close-qr-modal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold transition-transform hover:scale-110">&times;</button>
                <img id="qr-code-modal-img" src="" alt="QR Code" class="max-w-full max-h-[80vh] object-contain rounded-md" />
            </div>
        </div>

        <!-- Chart.js Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctxCollege = document.getElementById('collegePieChart').getContext('2d');
                new Chart(ctxCollege, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode(array_column($collegeCounts, 'label')) !!},
                        datasets: [{
                            data: {!! json_encode(array_column($collegeCounts, 'count')) !!},
                            backgroundColor: [
                                '#DDD6FE', // total
                                '#c77dff', // cics
                                '#90e0ef', // cted
                                '#ff4d6d', // ccje
                                '#ffc8dd', // chm
                                '#fae588', // cbea
                                '#80ed99'  // ca
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                align: 'center',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 12,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 10,
                                bottom: 10
                            }
                        }
                    }
                });

                // Bar Chart - Year Level Breakdown
                const ctxYear = document.getElementById('yearBarChart').getContext('2d');
                new Chart(ctxYear, {
                    type: 'bar',
                    data: {
                        labels: ['1st Year', '2nd Year', '3rd Year', '4th Year'],
                        datasets: [{
                            label: 'Students',
                            data: [
                                {{ $students->where('year', 1)->count() }},
                                {{ $students->where('year', 2)->count() }},
                                {{ $students->where('year', 3)->count() }},
                                {{ $students->where('year', 4)->count() }},
                            ],
                            backgroundColor: '#a2d2ff'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // College filter logic
                document.querySelectorAll('.college-filter').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const college = this.getAttribute('data-college');
                        document.querySelectorAll('#student-table-body tr').forEach(row => {
                            if (college === 'All' || row.getAttribute('data-college') === college) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                });
                // Select all logic
                document.getElementById('select-all').addEventListener('change', function() {
                    document.querySelectorAll('.select-student').forEach(cb => {
                        cb.checked = this.checked;
                    });
                });
                // Modal logic
                const modal = document.getElementById('batch-print-modal');
                const grid = document.getElementById('batch-print-grid');
                const closeModal = document.getElementById('close-batch-print');
                const modalPrintBtn = document.getElementById('modal-print-btn');
                // Print selected logic (modal version)
                document.getElementById('print-selected-btn').addEventListener('click', function() {
                    const selected = Array.from(document.querySelectorAll('.select-student:checked'));
                    console.log('Selected students:', selected.length);
                    if (selected.length === 0) {
                        alert('Select at least one student.');
                        return;
                    }
                    if (selected.length > 6) {
                        alert('You can only print up to 6 students at a time.');
                        return;
                    }
                    // Prepare data for print
                    const students = selected.map(cb => ({
                        name: cb.getAttribute('data-name'),
                        studentid: cb.getAttribute('data-studentid'),
                        qr: cb.getAttribute('data-qr'),
                    }));
                    // Fill grid
                    grid.innerHTML = '';
                    students.forEach(stu => {
                        grid.innerHTML += `<div class='qr-block bg-white rounded-lg shadow p-4 flex flex-col items-center border'>
                            <img src='${stu.qr}' alt='QR Code' class='w-32 h-32 mb-2 bg-white border rounded'>
                            <div class='name font-semibold text-base mb-1 text-center'>${stu.name}</div>
                            <div class='id text-gray-700 text-sm mb-1 text-center'>ID: ${stu.studentid}</div>
                        </div>`;
                    });
                    // Fill empty blocks if less than 6
                    for(let i=students.length; i<6; i++) {
                        grid.innerHTML += `<div class='qr-block'></div>`;
                    }
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.style.opacity = 1;
                    console.log('Modal opened, grid filled.');
                });
                // Close modal
                closeModal.addEventListener('click', function() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.style.opacity = 0;
                });
                // Print from modal
                modalPrintBtn.addEventListener('click', function() {
                    modal.classList.add('print-mode');
                    window.onafterprint = function() {
                        modal.classList.remove('print-mode');
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.style.opacity = 0;
                        window.onafterprint = null;
                    };
                    window.print();
                });
                // Close modal on backdrop click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.style.opacity = 0;
                    }
                });

                // QR Code modal logic
                const qrCodeModal = document.getElementById('qr-code-modal');
                const qrCodeModalImg = document.getElementById('qr-code-modal-img');
                const closeQrModalBtn = document.getElementById('close-qr-modal');

                document.querySelectorAll('td.px-4.py-4 img').forEach(img => {
                    img.style.cursor = 'pointer';
                    img.addEventListener('click', function() {
                        qrCodeModalImg.src = this.src;
                        qrCodeModal.classList.remove('opacity-0', 'pointer-events-none');
                        qrCodeModal.classList.add('opacity-100');
                        setTimeout(() => {
                            qrCodeModal.querySelector('div').classList.remove('scale-95');
                            qrCodeModal.querySelector('div').classList.add('scale-100');
                        }, 10);
                    });
                });

                closeQrModalBtn.addEventListener('click', () => {
                    qrCodeModal.querySelector('div').classList.remove('scale-100');
                    qrCodeModal.querySelector('div').classList.add('scale-95');
                    qrCodeModal.classList.remove('opacity-100');
                    qrCodeModal.classList.add('opacity-0', 'pointer-events-none');
                });

                qrCodeModal.addEventListener('click', (e) => {
                    if (e.target === qrCodeModal) {
                        closeQrModalBtn.click();
                    }
                });

                // Edit modal logic
                const editModal = document.getElementById('edit-student-modal');
                const closeEditModal = document.getElementById('close-edit-modal');
                const editForm = document.getElementById('edit-student-form');
                const editModalErrors = document.getElementById('edit-modal-errors');
                let currentEditId = null;
                let currentEditRow = null;
                document.querySelectorAll('a[title="Edit"]').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const row = this.closest('tr');
                            currentEditRow = row;
                            currentEditId = this.getAttribute('data-id');
                            document.getElementById('edit-student-id-hidden').value = currentEditId;
                            document.getElementById('edit-student-id').value = row.children[1].textContent.trim();
                            document.getElementById('edit-lname').value = row.children[2].textContent.trim();
                            document.getElementById('edit-fname').value = row.children[3].textContent.trim();
                            document.getElementById('edit-mi').value = row.children[4].textContent.trim();
                            document.getElementById('edit-college').value = row.children[5].querySelector('span').textContent.trim();
                            document.getElementById('edit-year').value = row.children[6].textContent.trim();
                            document.getElementById('edit-email').value = row.children[7].querySelector('a').textContent.trim();
                            // Set the form action dynamically to the update route with the current student ID
                            document.getElementById('edit-student-form').action = `/admin/students/${currentEditId}`;
                            editModalErrors.classList.add('hidden');
                            editModalErrors.innerHTML = '';
                            editModal.classList.remove('hidden');
                            editModal.classList.add('flex');
                            editModal.style.opacity = 1;
                        });
                });
                closeEditModal.addEventListener('click', function() {
                    editModal.classList.add('hidden');
                    editModal.classList.remove('flex');
                    editModal.style.opacity = 0;
                });
                editModal.addEventListener('click', function(e) {
                    if (e.target === editModal) {
                        editModal.classList.add('hidden');
                        editModal.classList.remove('flex');
                        editModal.style.opacity = 0;
                    }
                });
                editForm.addEventListener('submit', function(e) {
                    // Removed JavaScript fetch submission to use standard form submission instead
                });

                // Toast logic
                const toast = document.getElementById('toast');
                function showToast(message, type = 'success') {
                    toast.textContent = message;
                    toast.className = `fixed top-6 right-6 z-50 px-6 py-3 rounded shadow-lg text-white text-base font-medium transition-all duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
                    toast.classList.remove('hidden');
                    setTimeout(() => { toast.classList.add('hidden'); }, 2500);
                }

                // Show toast from Laravel session flash messages
                @if(session('success'))
                    showToast(@json(session('success')), 'success');
                @elseif(session('error'))
                    showToast(@json(session('error')), 'error');
                @endif

                // Delete modal logic
                const deleteModal = document.getElementById('delete-confirmation-modal');
                const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
                const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
                const closeDeleteModalBtn = document.getElementById('close-delete-modal');
                let formToDelete = null;

                document.querySelectorAll('form.inline').forEach(form => {
                    const deleteBtn = form.querySelector('button[type="submit"]');
                    // Remove inline confirm
                    deleteBtn.removeAttribute('onclick');
                    deleteBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        formToDelete = form;
                        deleteModal.classList.remove('opacity-0', 'pointer-events-none');
                        deleteModal.classList.add('opacity-100');
                        setTimeout(() => {
                            deleteModal.querySelector('div').classList.remove('scale-95');
                            deleteModal.querySelector('div').classList.add('scale-100');
                        }, 10);
                    });
                });

                function closeDeleteModal() {
                    deleteModal.querySelector('div').classList.remove('scale-100');
                    deleteModal.querySelector('div').classList.add('scale-95');
                    deleteModal.classList.remove('opacity-100');
                    deleteModal.classList.add('opacity-0', 'pointer-events-none');
                    formToDelete = null;
                }

                cancelDeleteBtn.addEventListener('click', closeDeleteModal);
                closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
                deleteModal.addEventListener('click', (e) => {
                    if (e.target === deleteModal) {
                        closeDeleteModal();
                    }
                });

                confirmDeleteBtn.addEventListener('click', () => {
                    if (formToDelete) {
                        formToDelete.submit();
                    }
                });
            });
        </script>
    </body>
</html>
