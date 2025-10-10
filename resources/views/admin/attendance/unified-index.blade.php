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
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        window.assetBaseUrl = "{{ asset('') }}";
        
        // Pusher configuration
        window.pusherConfig = {
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}',
            wsHost: '{{ config('broadcasting.connections.pusher.options.host', `ws-${config('broadcasting.connections.pusher.options.cluster', 'mt1')}.pusher.com`) }}',
            wsPort: {{ config('broadcasting.connections.pusher.options.port', 80) }},
            wssPort: {{ config('broadcasting.connections.pusher.options.port', 443) }},
            forceTLS: {{ config('broadcasting.connections.pusher.options.useTLS', true) ? 'true' : 'false' }},
            enabledTransports: ['ws', 'wss'],
            disableStats: true
        };
        
        // Log Pusher config for debugging
        console.log('[INIT] Pusher config:', window.pusherConfig);
    </script>
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
                                <h2 class="text-3xl font-bold text-gray-800">📸 Unified Attendance Scanner</h2>
                                <p class="text-sm text-gray-600 mt-1">One scanner for <span class="text-blue-600 font-semibold">Students</span> and <span class="text-purple-600 font-semibold">Teachers/Visitors</span></p>
                            </div>
                            <div class="flex space-x-2">
                                <button id="fullscreen-btn" onclick="toggleFullScreen()" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg">
                                    Full Screen
                                </button>
                                <x-attendance-menu />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">🎓 Students Supported</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">👨‍🏫 Teachers Supported</span>
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
                        <div id="scanner-loading" class="text-center p-8 bg-gray-100 rounded-lg">
                            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="mt-2 text-sm text-gray-600">Initializing scanner...</p>
                        </div>
                        <div id="qr-reader" class="my-4 mx-auto" style="width: 500px; min-height: 300px; display: none;"></div>
                        <div id="scanner-error" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg"></div>
                    </div>
                    <div id="physical-container" class="mb-4 hidden">
                        <label for="qr-input" class="block mb-2 text-sm font-medium text-gray-700">QR Scanner Input:</label>
                        <input type="text" 
                               id="qr-input" 
                               autocomplete="off" 
                               class="border border-gray-300 p-3 w-full max-w-md mx-auto block rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Scan QR code here..." 
                               autofocus>
                    </div>

                    <div class="my-6 p-6 bg-gradient-to-r from-blue-50 via-purple-50 to-blue-50 border-2 border-blue-300 rounded-xl text-center shadow-sm">
                        <p class="text-lg font-bold text-gray-800 mb-2">🎓 Scan Student OR 👨‍🏫 Teacher QR Code</p>
                        <p id="mode-description" class="text-sm text-gray-700">System automatically detects user type • Both use same scanner</p>
                    </div>

                    <!-- Overall Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                                    <p class="text-sm text-gray-600 mt-1">
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
                                    <p class="text-sm text-gray-600 mt-1">
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="teacher-attendance-table-body">
                                    @forelse($teacherAttendance as $attendance)
                                        <tr data-attendance-id="{{ $attendance['id'] }}" data-user-type="teacher">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['identifier'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                                                <img src="{{ \App\Services\AvatarService::getPlaceholderAvatar($attendance['name'], 100) }}"
                                                    alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-purple-100" />
                                                <span class="font-medium">{{ $attendance['name'] }}</span>
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
                                <input type="hidden" name="user_type" id="borrow-user-type" value="">
                                <input type="hidden" name="identifier" id="borrow-identifier" value="">

                                <!-- Search and Filter -->
                                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="available-books-search" class="block text-sm font-medium text-gray-700 mb-2">Search Books</label>
                                        <input type="text" id="available-books-search" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by title, author, or code...">
                                    </div>
                                    <div>
                                        <label for="available-books-college" class="block text-sm font-medium text-gray-700 mb-2">Filter by College</label>
                                        <select id="available-books-college" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">All Colleges</option>
                                            <option value="CICS">CICS</option>
                                            <option value="CTED">CTED</option>
                                            <option value="CCJE">CCJE</option>
                                            <option value="CHM">CHM</option>
                                            <option value="CBEA">CBEA</option>
                                            <option value="CA">CA</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" id="refresh-available-books" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                            🔄 Refresh List
                                        </button>
                                    </div>
                                </div>

                                <!-- Available Books Grid -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Available Books</h4>
                                    <div id="available-books-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                                        <!-- Books will be loaded here -->
                                    </div>
                                </div>

                                <!-- Manual Book Entry -->
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Or Enter Book Code Manually</h4>
                                    <div class="flex gap-2">
                                        <input type="text" id="manual-book-code" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter book code (e.g., LIB-001)">
                                        <button type="button" id="manual-borrow-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                            Borrow
                                        </button>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-3">
                                    <button type="button" id="borrow-modal-cancel" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">Cancel</button>
                                    <button type="submit" id="borrow-modal-submit" class="px-6 py-2 text-white rounded-lg transition-colors" style="background-color: #3b82f6;">Submit Borrow Request</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/unified-scan-attendance.js') }}"></script>
</body>
</html>
