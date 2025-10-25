<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Attendance Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/heroicons/2.0.18/heroicons.min.css">
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
        .attendance-fullscreen-active .main-content {
            margin-left: 0 !important;
        }
        .college-CICS { background-color: #e9d5ff; color: #454545; }  /* purple-200 */
        .college-CTED { background-color: #bfdbfe; color: #454545; }  /* blue-200 */
        .college-CCJE { background-color: #fecaca; color: #454545; }  /* red-200 */
        .college-CHM { background-color: #fbcfe8; color: #454545; }   /* pink-200 */
        .college-CBEA { background-color: #fef9c3; color: #454545; }  /* yellow-200 */
        .college-CA { background-color: #bbf7d0; color: #454545; }    /* green-200 */

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
                                <button id="fullscreen-btn" onclick="toggleFullScreen()" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg not-fullscreen-btn">
                                    Full Screen
                                </button>
                                <div class="not-fullscreen-btn not-fullscreen">
                                    <x-attendance-menu />
                                </div>
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Today's Overview</h3>
        <p class="text-xs text-gray-500 mt-0.5">Real-time attendance and study area stats</p>
    </div>

    <div class="p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

            <!-- Total Attendance -->
            <div class="flex items-center gap-3">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M9 13v5m4-10v10m4-7v7" />
                </svg>
                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $overallStats['total'] }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                </div>
            </div>

            <!-- Students Present -->
            <div class="flex items-center gap-3">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7m0 0l-3-1.5M12 21l3-1.5" />
                </svg>
                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $overallStats['students_present'] }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Students</p>
                </div>
            </div>

            <!-- Teachers Present -->
            <div class="flex items-center gap-3">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1m-6 6v-2a4 4 0 014-4h1m-6 6H3v-2a4 4 0 014-4h1m6-6a3 3 0 11-6 0 3 3 0 016 0zm8 3a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $overallStats['teachers_present'] }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Teachers</p>
                </div>
            </div>

            <!-- Study Area Status -->
            <div class="flex items-center gap-3">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5l9-7.5 9 7.5V21a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4H9v4a1 1 0 01-1 1H4a1 1 0 01-1-1V10.5z" />
                </svg>
                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <div id="study-area-badge" class="flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 max-w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1" id="status-dot"></span>
                        <span id="study-area-availability" class="truncate text-gray-700">Loading...</span>
                    </div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">Study Area</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end col-span-1 sm:col-span-2 lg:col-span-1">
                <form action="{{ route('admin.attendance.save-reset') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-black hover:bg-gray-900 text-white font-medium py-1.5 px-3 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 text-xs whitespace-nowrap">
                        Save & Reset
                    </button>
                </form>
            </div>

        </div>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 100px;">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 150px;">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">College</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 80px;">Gender</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 150px;">Activity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 80px;">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 80px;">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 100px;">Status</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="max-h-[400px] overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200" id="student-attendance-table-body" data-loaded="0" data-total="{{ count($studentAttendance) }}">
                                        <!-- Initial 10 records will be loaded by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
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
                                                <img src="{{ \App\Services\AvatarService::getProfilePictureUrl($attendance['profile_picture'], $attendance['name'], 100) }}"
                                                     class="h-10 w-10 rounded-full object-cover mr-3"
                                                     alt="{{ $attendance['name'] }}"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'">
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
                                                {{ $attendance['gender'] ?? 'N/A' }}
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

                    <!-- Logout Confirmation Modal -->
                    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96 max-w-md">
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Logout</h3>
                                <p class="text-sm text-gray-600 mb-4" id="logout-message">
                                    A 6-digit verification code was sent to your email. Enter it below to complete logout.
                                </p>

                                <div class="mb-4">
                                    <label for="logout-code" class="block mb-2 text-sm font-medium text-gray-700">Verification Code</label>
                                    <input type="text" id="logout-code" maxlength="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-lg font-mono" placeholder="000000">
                                </div>

                                <div id="logout-error-message" class="mb-4 text-sm text-red-600 hidden"></div>
                                <div id="logout-success-message" class="mb-4 text-sm text-green-600 hidden"></div>

                                <div class="flex justify-between space-x-2">
                                    <button type="button" id="logout-modal-cancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="button" id="logout-modal-confirm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Confirm Logout
                                    </button>
                                </div>

                                <div class="mt-4 text-xs text-gray-500">
                                    <p>Didn't receive the code? <button id="resend-code-btn" class="text-blue-600 hover:text-blue-800 underline">Resend</button></p>
                                </div>
                            </div>
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
                                                <img id="user-profile-pic" src="{{ \App\Services\AvatarService::getPlaceholderAvatarWithColor('User', 100, '3b82f6') }}"
                                                     alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 shadow-sm" id="profile-pic-border"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'">
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
                                        <option value="Stay to Study">Stay to Study</option>
                                        <option value="Borrow">Borrow Books</option>
                                        <option value="Stay&Borrow">Stay and Borrow Books</option>
                                        <option value="Other">Other Activities</option>
                                    </select>
                                </div>

                                <!-- Other Activities Section -->
                                <div id="other-activities-section" class="mb-4 opacity-0 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                    <label class="block mb-2 font-medium text-gray-700">Specify Other Activity</label>

                                    <!-- Predefined Activities -->
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 mb-2">Quick select:</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Sign Clearance">📰 Sign Clearance</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Past Time">⏰ Past Time</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Waiting for Class">📚 Waiting for Class</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Comfort Room">🚻 Comfort Room</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Research">🔍 Research</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Group Study">👥 Group Study</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Reading">📖 Reading</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Computer Use">💻 Computer Use</button>
                                            <button type="button" class="other-activity-btn px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-left" data-activity="Meeting">🤝 Meeting</button>
                                        </div>
                                    </div>

                                    <!-- Custom Activity Input -->
                                    <div>
                                        <label for="custom-activity" class="block text-sm text-gray-600 mb-1">Or enter custom activity:</label>
                                        <input type="text" id="custom-activity" name="custom_activity" placeholder="Enter your activity..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>
                                
                                <div class="flex justify-between space-x-2">
                                    <button type="button" id="modal-back" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-full border-2 border-transparent hover:bg-gray-400 hover:border-gray-600 transition-all duration-300 ease-in-out focus:outline-none flex items-center space-x-3 group">
                                    <span class="transform group-hover:translate-x-2 transition-all duration-300 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </span>
                                    <span>Back</span>
                                    </button>

                                    <div class="flex space-x-2">
                                        <button type="button" id="modal-cancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">Cancel</button>
                                        <button type="submit" id="modal-submit" class="px-4 py-2 text-white rounded-lg transition-colors" style="background-color: #3b82f6;">Log Attendance</button>
                                    </div>
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


                                <button type="button" id="book-selection-cancel" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-full border-2 border-transparent hover:bg-gray-400 hover:border-gray-600 transition-all duration-300 ease-in-out focus:outline-none flex items-center space-x-3 group">
                                <span class="transform group-hover:translate-x-2 transition-all duration-300 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                                <span>Back</span>
                                </button>

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

                    <!-- Study Area Full Modal -->
                    <div id="study-area-full-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[70]">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96 max-w-md">
                            <div class="text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Study Area Slot Warning</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    We're sorry, but all study area spaces are currently occupied (0 of 10 slots available).
                                    Students are already utilizing all available space.
                                    Please try again later or consider other activities that don't require study area access.
                                </p>
                                <div class="flex justify-center">
                                    <button type="button" id="study-area-full-ok" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        I Understand
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fullscreen Exit Password Modal -->
                    <div id="fullscreen-exit-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[70]">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Exit Fullscreen Mode</h3>
                                <p class="text-sm text-gray-600 mt-1">Enter the password to exit fullscreen mode.</p>
                            </div>
                            <form id="fullscreen-exit-form">
                                <div class="mb-4">
                                    <label for="exit-password" class="block mb-1 font-medium text-gray-700">Password</label>
                                    <input type="password" id="exit-password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password..." required>
                                </div>
                                <div id="exit-error-message" class="mb-4 text-sm text-red-600 hidden">Access denied. Only authorized library staff can exit fullscreen mode.</div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" id="exit-modal-cancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-white rounded-lg transition-colors" style="background-color: #3b82f6;">Exit Fullscreen</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass student attendance data to JavaScript
        window.studentAttendanceData = @json($studentAttendance);
    </script>
    <script src="{{ asset('js/unified-scan-attendance.js') }}"></script>
    <script src="{{ asset('js/study-area.js') }}"></script>
</body>
</html>
