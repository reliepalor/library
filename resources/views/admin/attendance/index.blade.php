<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Attendance Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .shadcn-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
        .main-content { transition: margin-left 0.5s ease-in-out; }
        .sidebar-collapsed { margin-left: 4rem; }
        .sidebar-expanded { margin-left: 15rem; }
        @media (max-width: 768px) {
            .sidebar-collapsed, .sidebar-expanded { margin-left: 0; }
        }
        .college-CICS { background-color: #c77dff; }
        .college-CTED { background-color: #90e0ef; }
        .college-CCJE { background-color: #ff4d6d; }
        .college-CHM { background-color: #ffc8dd; }
        .college-CBEA { background-color: #fae588; }
        .college-CA { background-color: #80ed99; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.3s ease-out forwards; }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
    <script>window.assetBaseUrl = "{{ asset('') }}";</script>
</head>
<body class="bg-gray-50" data-attendance-page="unified">
    <div class="flex h-screen" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <div class="not-fullscreen">
            <x-admin-nav-bar />
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto" :class="sidebarExpanded ? 'sidebar-expanded' : 'sidebar-collapsed'">
            <div class="container mx-auto px-4 py-8">
                <div id="fullscreen-section" class="fullscreen transition-all duration-500 ease-in-out bg-white overflow-auto relative px-5 py-2 rounded-lg">
                    
                    <!-- QR Attendance Logging -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">Unified Attendance Scanner</h2>
                            </div>
                            <div class="flex space-x-2">
                                <button id="fullscreen-btn" onclick="toggleFullScreen()" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg">
                                    Full Screen
                                </button>
                                <x-attendance-menu />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-800 rounded-full text-xs font-semibold">Students Supported</span>
                            <span class="px-3 py-1 bg-purple-50 text-purple-800 rounded-full text-xs font-semibold">Teachers Supported</span>
                        </div>
                    </div>

                    <!-- Scanner Mode Toggle -->
                    <div class="flex justify-center mb-6">
                        <div class="bg-gray-100 p-2 rounded-lg flex space-x-2">
                            <button id="physical-mode-btn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-medium transition-colors hover:bg-gray-400">
                                🔍 Physical Scanner
                            </button>
                            <button id="webcam-mode-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md font-medium transition-colors">
                                📷 Webcam Scanner
                            </button>
                        </div>
                    </div>

                    <!-- Scanner Containers -->
                    <div id="webcam-container" class="mb-4">
                        <div id="qr-reader" class="my-4 mx-auto" style="width: 500px;"></div>
                    </div>
                    <div id="physical-container" class="mb-4 hidden">
                        <label for="qr-input" class="block mb-2 text-sm font-medium text-gray-700">QR Scanner Input:</label>
                        <input type="text" id="qr-input" autocomplete="off" class="border border-gray-300 p-3 w-full max-w-md mx-auto block rounded-lg" placeholder="Scan QR code here..." autofocus>
                    </div>

                    <div class="my-6 p-6 bg-gradient-to-r from-blue-50 via-purple-50 to-blue-50 border-2 border-blue-300 rounded-xl text-center shadow-sm">
                        <p class="text-lg font-bold text-gray-800 mb-2">Scan Student or Teacher QR Code</p>
                        <p id="mode-description" class="text-sm text-gray-700">System automatically detects user type • Both use same scanner</p>
                    </div>

                    <!-- Overall Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Attendance</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['total'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">👥</span>
                                </div>
                            </div>
                        </div>
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Students Present</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $overallStats['students_present'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">🎓</span>
                                </div>
                            </div>
                        </div>
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Teachers Present</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ $overallStats['teachers_present'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">👨‍🏫</span>
                                </div>
                            </div>
                        </div>
                        <div class="shadcn-card p-4">
                            <div class="flex flex-col justify-center space-y-2">
                                <button id="refresh-btn" onclick="refreshAttendanceTable()" class="w-full bg-blue-600 px-4 py-2 text-white rounded-lg font-bold hover:bg-blue-700 duration-100 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4 animate-spin hidden" id="refresh-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span id="refresh-text">🔄 Refresh</span>
                                </button>
                            </div>
                        </div>
                        <div class="shadcn-card p-4">
                            <form action="{{ route('admin.attendance.save-reset') }}" method="POST" class="h-full flex flex-col justify-center">
                                @csrf
                                <button type="submit" class="w-full bg-gray-800 px-4 py-2 text-white rounded-lg font-bold hover:bg-gray-700 duration-100">
                                    💾 Save & Reset
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Students Attendance Table -->
                    <div class="shadcn-card overflow-hidden mb-6">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Students Attendance</h2>
                                    <p class="text-sm text-gray-600 mt-1 hidden">
                                        Total: {{ $studentStats['total'] }} | Present: {{ $studentStats['present'] }} | Logged Out: {{ $studentStats['logged_out'] }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-semibold">{{ $studentStats['total'] }}</span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">College</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="student-attendance-table-body">
                                    @forelse($studentAttendance as $attendance)
                                        <tr data-attendance-id="{{ $attendance['id'] }}" data-user-type="student">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['identifier'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                                                <img src="{{ $attendance['profile_picture'] ?? \App\Services\AvatarService::getPlaceholderAvatar($attendance['name'], 100) }}"
                                                    alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100" />
                                                <span class="font-medium">{{ $attendance['name'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-{{ $attendance['college_or_dept'] }}">
                                                    {{ $attendance['college_or_dept'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if(str_contains(strtolower($attendance['activity']), 'wait for approval'))
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $attendance['activity'] }}</span>
                                                @elseif(str_contains(strtolower($attendance['activity']), 'borrow:'))
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $attendance['activity'] }}</span>
                                                @else
                                                    {{ $attendance['activity'] }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['time_in'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['time_out'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance['time_out'] === 'N/A')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Logged Out</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No student attendance records for today</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Teachers Attendance Table -->
                    <div class="shadcn-card overflow-hidden mb-6">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Teachers & Visitors Attendance</h2>
                                    <p class="text-sm text-gray-600 mt-1 hidden">
                                        Total: {{ $teacherStats['total'] }} | Present: {{ $teacherStats['present'] }} | Logged Out: {{ $teacherStats['logged_out'] }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-purple-500 text-white rounded-full text-sm font-semibold">{{ $teacherStats['total'] }}</span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profile</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="teacher-attendance-table-body">
                                    @forelse($teacherAttendance as $attendance)
                                        <tr data-attendance-id="{{ $attendance['id'] }}" data-user-type="teacher">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $attendance['role'] === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $attendance['role'] === 'teacher' ? 'text-blue-600' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    {{ ucfirst($attendance['role'] ?? 'staff') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <img src="{{ \App\Services\AvatarService::getPlaceholderAvatar($attendance['name'], 100) }}" 
                                                         class="h-10 w-10 rounded-full object-cover mr-3" 
                                                         alt="{{ $attendance['name'] }}"
                                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($attendance['name'] ?? 'User') }}&background=random&size=100'">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $attendance['name'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $attendance['identifier'] ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    {{ $attendance['college_or_dept'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 py-1 text-xs font-medium rounded {{ $attendance['role'] === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($attendance['role']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['activity'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['time_in'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['time_out'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance['time_out'] === 'N/A')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Logged Out</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No teacher attendance records for today</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Activity Modal (Unified for both user types) -->
                    <div id="activity-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96" id="modal-container">
                            <!-- Dynamic Header -->
                            <div class="mb-4 p-3 rounded-lg" id="modal-header">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold" id="modal-title">Select Activity</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" id="user-type-badge"></span>
                                </div>
                            </div>
                            
                            <form id="activity-form">
                                @csrf
                                <input type="hidden" name="user_type" id="modal-user-type" value="">
                                <input type="hidden" name="identifier" id="modal-identifier" value="">
                                
                                <div id="user-info" class="mb-4 p-4 rounded-lg" style="border: 2px solid #e5e7eb;">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="relative">
                                                <img id="user-profile-pic" src="{{ \App\Services\AvatarService::getPlaceholderAvatar('User', 100) }}" 
                                                     alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 shadow-sm" id="profile-pic-border">
                                                <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full flex items-center justify-center text-xl" id="user-type-icon"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div id="user-details" class="space-y-1">
                                                <p class="text-sm font-medium text-gray-700">Loading...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="activity" class="block mb-1 font-medium text-gray-700">Activity</label>
                                    <select name="activity" id="activity" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="Study">Study</option>
                                        <option value="Borrow">Borrow Books</option>
                                        <option value="Stay&Borrow">Stay and Borrow Books</option>
                                        <option value="Other">Other Activities</option>
                                    </select>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" id="modal-cancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">Cancel</button>
                                    <button type="submit" id="modal-submit" class="px-4 py-2 text-white rounded-lg transition-colors" style="background-color: #3b82f6;">Log Attendance</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Book Selection Modal -->
                    <div id="book-selection-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-hidden">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">Select Books to Borrow</h3>
                                    <p class="text-sm text-gray-600 mt-1">Choose from available books in the library</p>
                                </div>
                                <button type="button" id="book-selection-cancel" class="px-3 py-1.5 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Close</button>
                            </div>
                            
                            <!-- Search and Filters -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-1">
                                            <input id="available-books-search" type="text" placeholder="Search by title, author, code, or section..." class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                                            </svg>
                                        </div>
                                        <select id="available-books-college" class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">All Colleges</option>
                                        </select>
                                        <button id="refresh-available-books" class="px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">Refresh</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Books Grid -->
                            <div class="p-6">
                                <div id="available-books-container" class="border border-gray-200 rounded-xl max-h-[32rem] overflow-y-auto bg-white">
                                    <div class="p-4 text-sm text-gray-500">Loading available books...</div>
                                    <div id="available-books-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4"></div>
                                </div>
                            </div>
                            
                            <!-- Manual Entry Section -->
                            <div class="p-6 border-t border-gray-100 bg-gray-50">
                                <div class="max-w-md mx-auto">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Or Enter Book Code Manually</h4>
                                    <form id="manual-borrow-form" class="flex gap-3">
                                        @csrf
                                        <input type="hidden" name="user_type" id="manual-user-type" value="">
                                        <input type="hidden" name="identifier" id="manual-identifier" value="">
                                        <input type="text" name="book_id" id="manual-book-id" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., BK-00123" required>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Request Borrow</button>
                                    </form>
                                    <p class="mt-2 text-xs text-gray-500">Tip: You can also click a book from the list above to auto-fill the code.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/unified-scan-attendance.js') }}"></script>
</body>
</html>
