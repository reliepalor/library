<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .shadcn-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .college-CICS { background-color: #c77dff; }
        .college-CTED { background-color: #90e0ef;  }
        .college-CCJE { background-color: #ff4d6d; }
        .college-CHM { background-color: #ffc8dd;  }
        .college-CBEA { background-color: #fae588;  }
        .college-CA { background-color: #80ed99; }


            @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.3s ease-out forwards; }
    #dropdownMenu.show {
        transform: scaleY(1);
        opacity: 1;
        display: block;
    }
    #dropdownButton[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
    a:hover svg {
        color: #3B82F6;
    }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

    <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <div class="not-fullscreen">
            <x-admin-nav-bar />
        </div>
        
        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8" id="page-container" >

           <div id="fullscreen-section" class="fullscreen transition-all duration-500 ease-in-out bg-white overflow-auto relative px-5 py-2 rounded-lg"
    id="fullscreen-section">
                <!-- QR Attendance Logging (moved from user) -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">QR Attendance Logging</h2>
                    <div class="flex space-x-2">
                        <div>
                            <button 
                            id="fullscreen-btn"
                                onclick="toggleFullScreen()"
                                class="mb-4 px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg"
                            >
                                Full Screen
                            </button>
                        </div>
                        
                        <div>
                            <x-attendance-menu />
                        </div>
                    </div>
                    
                </div>
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
                <div id="webcam-container" class="mb-4">
                    <div id="qr-reader" class="my-4 mx-auto" style="width: 500px;"></div>
                </div>
                <div id="physical-container" class="mb-4 ">
                    <label for="qr-input" class="block mb-2 text-sm font-medium text-gray-700">QR Scanner Input:</label>
                    <input type="text" id="qr-input" autocomplete="off"
                        class="border border-gray-300 p-3 w-full max-w-md mx-auto block rounded-lg" 
                        placeholder="Scan QR code here..." autofocus>
                </div>
                <div class="my-6 p-6 bg-blue-50 border border-blue-200 rounded-lg text-center">
                    <p class="text-lg font-medium text-blue-800">Scan student QR code to log attendance</p>
                    <p id="mode-description" class="text-sm text-blue-600">Using webcam scanner - point camera at QR code</p>
                </div>
                <div id="status-display" class="mb-4 p-3 bg-gray-50 rounded-lg hidden">
                    <p id="status-text" class="text-sm text-gray-700"></p>
                </div>
                <!-- Activity Modal -->
                <div id="activity-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4">Select Activity</h3>
                        <form id="activity-form">
                            @csrf
                            <input type="hidden" name="student_id" id="modal-student-id" value="">
                            <div class="mb-4">
                                <label for="activity" class="block mb-1 font-medium">Activity</label>
                                <select name="activity" id="activity" class="w-full border border-gray-300 rounded px-3 py-2">
                                    <option value="Study">Study</option>
                                    <option value="Borrow">Borrow Books</option>
                                    <option value="Stay&Borrow">Stay and Borrow Books</option>
                                    <option value="Other">Other Activities</option>
                                </select>
                            </div>
                            <div id="student-info" class="mb-4 p-3 bg-gray-50 rounded">
                                <p class="text-sm font-medium text-gray-700">Loading student information...</p>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" id="modal-cancel" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                <button type="submit" id="modal-submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Log Attendance</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Borrow Books Modal -->
                <div id="borrow-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4">Borrow Books</h3>
                        <form id="borrow-form">
                            @csrf
                            <input type="hidden" name="student_id" id="borrow-student-id" value="">
                            <div class="mb-4">
                                <label for="book_id" class="block mb-1 font-medium">Book ID</label>
                                <input type="text" name="book_id" id="book_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" id="borrow-cancel" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Request Borrow</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Other Activities Modal -->
                <div id="other-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4">Other Activities</h3>
                        <form id="other-form">
                            @csrf
                            <input type="hidden" name="student_id" id="other-student-id" value="">
                            <input type="hidden" name="activity" value="Other">
                            <div class="mb-4">
                                <label for="custom_activity" class="block mb-1 font-medium">Activity Description</label>
                                <input type="text" name="custom_activity" id="custom_activity" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" id="other-cancel" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Log Activity</button>
                            </div>
                        </form>
                    </div>
                </div>
    
                <!-- Logout Success Modal -->
                <div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50">
                    <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-xl shadow-lg p-6 w-96 text-center border border-white border-opacity-30">
                        <h3 class="text-xl font-semibold mb-4 text-white drop-shadow-md">Logout Successful</h3>
                        <p class="mb-4 text-white drop-shadow-sm">You have been logged out successfully.</p>
                        <button id="logout-close" class="px-4 py-2 bg-blue-600 bg-opacity-80 hover:bg-opacity-100 text-white rounded-lg shadow-md transition duration-300">Close</button>
                    </div>
                </div>

                <!-- Today's Attendance Table -->
                <div class="shadcn-card overflow-hidden mb-5">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Today's Attendance</h2>
                        <form action="{{ route('admin.attendance.save-reset') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class=" bg-gray-800 px-4 py-2 text-white rounded-lg text-bold hover:bg-gray-700 duration-100">
                                Save and Reset
                            </button>
                        </form>

                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">College</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="attendance-table-body">
                                    @forelse($todayAttendance as $attendance)
                                        <tr data-student-id="{{ $attendance['student_id'] }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['student_id'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                                                <img src="{{ $attendance['profile_picture'] ? asset('storage/' . $attendance['profile_picture']) : asset('images/default-profile.png') }}"
                                                    alt="Profile Picture"
                                                    class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100 transition-transform duration-300 hover:scale-105" />
                                                <span class="font-medium">{{ $attendance['student_name'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-{{ $attendance['college'] }}">{{ $attendance['college'] }}</span>
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
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No attendance records for today
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
    



</body>

</html>
