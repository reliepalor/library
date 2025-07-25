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
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <x-admin-nav-bar />


        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Attendance History</h1>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('admin.attendance.index') }}" class="shadcn-button">
                        Attendance
                    <a href="{{ route('admin.attendance.history') }}" class="shadcn-button">
                    View History
                </a>
                <a href="{{ route('admin.attendance.analytics') }}" class="shadcn-button">
                    Analytics
                </a>
                    <a href="{{ route('admin.attendance.insights') }}" class="shadcn-button">
                        Insights
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Students -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-gray-600">Total Students</h2>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Currently Present -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-gray-600">Currently Present</h2>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['present'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Logged Out -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-gray-600">Logged Out</h2>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['absent'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Books Borrowed -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-gray-600">Books Borrowed Today</h2>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['borrowed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Attendance Logging (moved from user) -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">QR Attendance Logging</h2>
                <button 
                    onclick="toggleFullScreen()" 
                    class="px-3 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg"
                >
                    Full Screen
                </button>
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
            <!-- Logout Confirmation Modal -->
            <!-- Removed logout confirmation modal as per user request -->
 
             <!-- Logout Success Modal -->
             <div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50">
                 <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-xl shadow-lg p-6 w-96 text-center border border-white border-opacity-30">
                     <h3 class="text-xl font-semibold mb-4 text-white drop-shadow-md">Logout Successful</h3>
                     <p class="mb-4 text-white drop-shadow-sm">You have been logged out successfully.</p>
                     <button id="logout-close" class="px-4 py-2 bg-blue-600 bg-opacity-80 hover:bg-opacity-100 text-white rounded-lg shadow-md transition duration-300">Close</button>
                 </div>
             </div>

            <!-- Today's Attendance Table -->
            <div class="shadcn-card overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Attendance</h2>
                    <form action="{{ route('admin.attendance.save-reset') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="shadcn-button">
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($todayAttendance as $attendance)
                                <tr>
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

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .shadcn-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
        .shadcn-button {
            background-color: #18181b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .shadcn-button:hover {
            background-color: #27272a;
        }
        .college-CICS { background-color: #c77dff; }
        .college-CTED { background-color: #90e0ef;  }
        .college-CCJE { background-color: #ff4d6d; }
        .college-CHM { background-color: #ffc8dd;  }
        .college-CBEA { background-color: #fae588;  }
        .college-CA { background-color: #80ed99; }
    </style>
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
    // Main JavaScript for admin scan/webcam attendance
    // (Copied and adapted from user attendance, all AJAX endpoints use /admin/attendance/...)
    document.addEventListener('DOMContentLoaded', () => {
        // DOM Elements
        const qrInput = document.getElementById('qr-input');
        const qrReader = document.getElementById('qr-reader');
        const webcamContainer = document.getElementById('webcam-container');
        const physicalContainer = document.getElementById('physical-container');
        const webcamModeBtn = document.getElementById('webcam-mode-btn');
        const physicalModeBtn = document.getElementById('physical-mode-btn');
        const modeDescription = document.getElementById('mode-description');
        const activityModal = document.getElementById('activity-modal');
        const borrowModal = document.getElementById('borrow-modal');
        const otherModal = document.getElementById('other-modal');
        const modalStudentId = document.getElementById('modal-student-id');
        const borrowStudentId = document.getElementById('borrow-student-id');
        const otherStudentId = document.getElementById('other-student-id');
        const studentInfoDiv = document.getElementById('student-info');
        const activitySelect = document.getElementById('activity');
        const statusDisplay = document.getElementById('status-display');
        const statusText = document.getElementById('status-text');
        const logoutModal = document.getElementById('logout-modal');
        const logoutClose = document.getElementById('logout-close');

        // Verify all required elements exist
        if (!qrInput || !qrReader || !webcamContainer || !physicalContainer || 
            !webcamModeBtn || !physicalModeBtn || !modeDescription || !activityModal || 
            !borrowModal || !otherModal || !modalStudentId || !borrowStudentId || 
            !otherStudentId || !studentInfoDiv || !activitySelect || !statusDisplay || 
            !statusText || !logoutModal || !logoutClose) {
            console.error('Required elements not found');
            return;
        }

        let isProcessing = false;
        let html5QrCode = null;
        let currentMode = 'webcam'; // 'webcam' or 'physical'
        let scannerRunning = false;
        let justLoggedOut = false;
        let logoutInProgress = false;

        // Add transition classes for smooth fade
        webcamContainer.classList.add('transition-opacity', 'duration-500', 'ease-in-out');
        physicalContainer.classList.add('transition-opacity', 'duration-500', 'ease-in-out');

        // Utility functions
        const showStatus = (message, type = 'info') => {
            statusText.textContent = message;
            statusDisplay.className = `mb-4 p-3 rounded-lg ${type === 'error' ? 'bg-red-50 border border-red-200' : type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200'}`;
            statusDisplay.classList.remove('hidden');
            setTimeout(() => {
                statusDisplay.classList.add('hidden');
            }, 4000);
        };

        const getCSRFToken = () => {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
        };

        // Mode switching functions
        const switchToWebcamMode = () => {
            currentMode = 'webcam';
            physicalContainer.classList.add('opacity-0');
            setTimeout(() => {
                physicalContainer.classList.add('hidden');
                webcamContainer.classList.remove('hidden');
                setTimeout(() => webcamContainer.classList.remove('opacity-0'), 10);
            }, 300);
            webcamContainer.classList.remove('opacity-0');
            webcamModeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-md font-medium transition-colors';
            physicalModeBtn.className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-medium transition-colors hover:bg-gray-400';
            modeDescription.textContent = 'Using webcam scanner - point camera at QR code';
            if (!scannerRunning) {
                initWebcamScanner();
            }
        };

        const switchToPhysicalMode = () => {
            currentMode = 'physical';
            webcamContainer.classList.add('opacity-0');
            setTimeout(() => {
                webcamContainer.classList.add('hidden');
                physicalContainer.classList.remove('hidden');
                setTimeout(() => {
                    physicalContainer.classList.remove('opacity-0');
                    qrInput.value = '';
                    qrInput.focus();
                }, 10);
            }, 300);
            physicalContainer.classList.remove('opacity-0');
            webcamModeBtn.className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-medium transition-colors hover:bg-gray-400';
            physicalModeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-md font-medium transition-colors';
            modeDescription.textContent = 'Using physical scanner - scan QR code in the input field';
            stopWebcamScanner();
        };

        // Initialize webcam scanner
        const initWebcamScanner = () => {
            if (scannerRunning || currentMode !== 'webcam') return;
            try {
                html5QrCode = new Html5Qrcode('qr-reader');
                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        handleQrScan(decodedText);
                    },
                    (error) => {}
                ).then(() => {
                    scannerRunning = true;
                }).catch(err => {
                    qrReader.innerHTML = '<p class="text-orange-600 text-center">Camera access failed. Please allow camera access or use physical scanner.</p>';
                    scannerRunning = false;
                });
            } catch (err) {
                qrReader.innerHTML = '<p class="text-red-600 text-center">Camera initialization failed. Use physical scanner instead.</p>';
                scannerRunning = false;
            }
        };

        // Stop webcam scanner
        const stopWebcamScanner = () => {
            if (html5QrCode && scannerRunning) {
                try {
                    html5QrCode.stop().then(() => {
                        scannerRunning = false;
                    }).catch(err => {
                        scannerRunning = false;
                    });
                } catch (err) {
                    scannerRunning = false;
                }
            }
        };

        // QR Input event listeners (Physical Scanner Mode)
        let inputTimeout;
        let qrParts = [];
        qrInput.addEventListener('input', (e) => {
            const value = e.target.value.trim();
            console.log('Scanner input value:', value); // Debug log
            // If the value contains three pipes, it's a full QR code
            if ((value.match(/\|/g) || []).length === 3) {
                handleQrScan(value);
                qrInput.value = '';
                qrParts = [];
                return;
            }
            if (inputTimeout) clearTimeout(inputTimeout);
            if (value.includes('|')) {
                qrParts.push(value.replace('|', '').trim());
                if (qrParts.length === 4) {
                    const completeQR = qrParts.join('|');
                    handleQrScan(completeQR);
                    qrInput.value = '';
                    qrParts = [];
                    return;
                }
                qrInput.value = '';
                return;
            }
            if (value) {
                inputTimeout = setTimeout(() => {
                    if (value === qrInput.value.trim()) {
                        qrParts.push(value.trim());
                        if (qrParts.length === 4) {
                            const completeQR = qrParts.join('|');
                            handleQrScan(completeQR);
                            qrInput.value = '';
                            qrParts = [];
                        }
                    }
                }, 50);
            }
        });
        qrInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = qrInput.value.trim();
                if (value) {
                    qrParts.push(value.trim());
                    if (qrParts.length === 4) {
                        const completeQR = qrParts.join('|');
                        handleQrScan(completeQR);
                        qrInput.value = '';
                        qrParts = [];
                    }
                }
            }
        });
        qrInput.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (pastedText) {
                handleQrScan(pastedText.trim());
                qrInput.value = '';
                qrParts = [];
            }
        });
        qrInput.addEventListener('focus', () => {
            if (currentMode === 'physical') {
                qrInput.value = '';
                qrParts = [];
            }
        });
        qrInput.addEventListener('blur', () => {
            const value = qrInput.value.trim();
            if (value && currentMode === 'physical' && !isProcessing) {
                qrParts.push(value.trim());
                if (qrParts.length === 4) {
                    const completeQR = qrParts.join('|');
                    handleQrScan(completeQR);
                    qrInput.value = '';
                    qrParts = [];
                }
            }
        });

        // Main QR scan handler
        const handleQrScan = async (qrData) => {
            if (isProcessing || logoutInProgress) return;
            isProcessing = true;
            showStatus('Processing QR code...');
            try {
                // Fix: Only allow printable ASCII and pipe
                const cleanData = qrData.replace(/[^-|]/g, '').trim();
                const parts = cleanData.split('|').map(part => part.trim());
                if (parts.length !== 4) throw new Error('Invalid QR code format. Expected student ID, name, college, and year.');
                const [studentId, name, college, yearRaw] = parts;
                if (!studentId || !name || !college || !yearRaw) throw new Error('QR code missing required fields.');
                let year = yearRaw;
                const yearMatch = yearRaw.match(/\d+/);
                if (yearMatch) year = yearMatch[0];
                const csrfToken = getCSRFToken();
                if (!csrfToken) throw new Error('CSRF token not found. Please refresh the page.');
                // Fetch student data
                const response = await fetch(`/admin/attendance/scan?student_id=${encodeURIComponent(studentId)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) throw new Error(`Server error: ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                if (!data.students) throw new Error('Student not found in database');
            // Check for active session
            const checkResponse = await fetch(`/admin/attendance/check?student_id=${encodeURIComponent(studentId)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (!checkResponse.ok) throw new Error(`Server error: ${checkResponse.status}`);
            const checkData = await checkResponse.json();

            if (checkData.hasActiveSession) {
                // If student is logged in, automatically log them out without showing activity modal or confirmation
                const logoutResponse = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        activity: checkData.activity || 'Logout'
                    }),
                });
                if (!logoutResponse.ok) throw new Error('Failed to log out student');
                const logoutData = await logoutResponse.json();
                showStatus(logoutData.message || 'Logout successful!', 'success');
                // Update attendance table dynamically
                addAttendanceRow({
                    student_id: studentId,
                    student_name: studentInfoDiv.querySelector('p.font-medium')?.textContent || '',
                    college: studentInfoDiv.querySelector('p.text-sm.text-gray-600:nth-child(3)')?.textContent.replace('College: ', '') || '',
                    activity: checkData.activity || 'Logout',
                    time_in: '', // No change for login time
                    time_out: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    status: 'Logged Out'
                });
                // Show logout success modal
                logoutModal.classList.remove('hidden');
                isProcessing = false;
                return;
            }

                // If not logged in, show the activity modal for login
                modalStudentId.value = studentId;
                const nameParts = name.split(' ');
                const fname = nameParts[0] || '';
                const lname = nameParts.slice(1).join(' ') || '';
                studentInfoDiv.innerHTML = `
                    <p class="font-medium text-gray-800">${lname}, ${fname}</p>
                    <p class="text-sm text-gray-600">ID: ${studentId}</p>
                    <p class="text-sm text-gray-600">College: ${college}</p>
                    <p class="text-sm text-gray-600">Year: ${year}</p>
                `;
                activitySelect.value = 'Study';
                activityModal.classList.remove('hidden');
                showStatus('Student found! Please select activity for login.', 'success');
            } catch (error) {
                showStatus(`Error: ${error.message}`, 'error');
                activityModal.classList.add('hidden');
                borrowModal.classList.add('hidden');
                otherModal.classList.add('hidden');
                logoutModal.classList.add('hidden');
            } finally {
                isProcessing = false;
                setTimeout(() => {
                    if (currentMode === 'physical') {
                        qrInput.focus();
                    }
                }, 500);
            }
        };

        // Mode toggle event listeners
        webcamModeBtn.addEventListener('click', switchToWebcamMode);
        physicalModeBtn.addEventListener('click', switchToPhysicalMode);

        // Activity selection handler
        activitySelect.addEventListener('change', function() {
            const selectedActivity = this.value;
            if (selectedActivity === 'Borrow' || selectedActivity === 'Stay&Borrow') {
                activityModal.classList.add('hidden');
                borrowStudentId.value = modalStudentId.value;
                borrowModal.classList.remove('hidden');
                setTimeout(() => document.getElementById('book_id').focus(), 100);
            } else if (selectedActivity === 'Other') {
                activityModal.classList.add('hidden');
                otherStudentId.value = modalStudentId.value;
                otherModal.classList.remove('hidden');
                setTimeout(() => document.getElementById('custom_activity').focus(), 100);
            }
        });

        // Activity form submission
        document.getElementById('activity-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = modalStudentId.value;
            const activity = activitySelect.value;
            try {
                showStatus('Logging attendance...');
                const response = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: activity 
                    }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to log attendance');
                showStatus(data.message || 'Attendance logged successfully!', 'success');
                activityModal.classList.add('hidden');
                // Dynamically add the new attendance row to the table
                addAttendanceRow({
                    student_id: studentId,
                    student_name: studentInfoDiv.querySelector('p.font-medium')?.textContent || '',
                    college: studentInfoDiv.querySelector('p.text-sm.text-gray-600:nth-child(3)')?.textContent.replace('College: ', '') || '',
                    activity: activity,
                    time_in: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    time_out: 'N/A',
                    status: 'Present'
                });
                // Reset for next scan
                if (currentMode === 'physical') {
                    qrInput.value = '';
                    qrInput.focus();
                }
            } catch (error) {
                showStatus(`Error: ${error.message}`, 'error');
            }
        });

        // Borrow form submission
        document.getElementById('borrow-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = borrowStudentId.value;
            const bookId = document.getElementById('book_id').value;
            const activityType = activitySelect.value === 'Stay&Borrow' ? 'Stay&Borrow' : 'Borrow';
            try {
                showStatus('Processing borrow request...');
                // First check if book can be borrowed (admin route)
                const borrowResponse = await fetch('/admin/borrow/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        book_id: bookId 
                    }),
                });
                if (!borrowResponse.ok) {
                    const errorData = await borrowResponse.json().catch(() => ({}));
                    showStatus(errorData.message || 'Failed to request book', 'error');
                    borrowModal.classList.add('hidden');
                    document.getElementById('book_id').value = '';
                    return;
                }
                // Only log attendance if book borrow request was successful
                const attendanceResponse = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: `${activityType}:${bookId}`
                    }),
                });
                if (!attendanceResponse.ok) {
                    const errorData = await attendanceResponse.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to log attendance');
                }
                const data = await borrowResponse.json();
                showStatus(data.message || 'Book borrow request successful!', 'success');
                borrowModal.classList.add('hidden');
                document.getElementById('book_id').value = '';
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } catch (error) {
                showStatus(`Error processing borrow request: ${error.message}`, 'error');
            }
        });

        // Other activities form submission
        document.getElementById('other-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = otherStudentId.value;
            const customActivity = document.getElementById('custom_activity').value;
            const activity = `Other: ${customActivity}`;
            try {
                showStatus('Logging custom activity...');
                const response = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: activity 
                    }),
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to log attendance');
                }
                const data = await response.json();
                showStatus(data.message || 'Activity logged successfully!', 'success');
                otherModal.classList.add('hidden');
                document.getElementById('custom_activity').value = '';
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } catch (error) {
                showStatus(`Error logging custom activity: ${error.message}`, 'error');
            }
        });

        // Modal cancel buttons
        document.getElementById('modal-cancel').addEventListener('click', () => {
            activityModal.classList.add('hidden');
            if (currentMode === 'physical') {
                qrInput.focus();
            }
        });
        document.getElementById('borrow-cancel').addEventListener('click', () => {
            borrowModal.classList.add('hidden');
            activityModal.classList.remove('hidden');
        });
        document.getElementById('other-cancel').addEventListener('click', () => {
            otherModal.classList.add('hidden');
            activityModal.classList.remove('hidden');
        });
        // Auto-focus QR input when in physical mode
        const focusInput = () => {
            if (currentMode === 'physical') {
                qrInput.focus();
            }
        };
        // Handle clicks outside modals to refocus input
        document.body.addEventListener('click', (event) => {
            const modals = [activityModal, borrowModal, otherModal];
            const clickedInsideModal = modals.some(modal =>
                !modal.classList.contains('hidden') && modal.contains(event.target)
            );
            if (!clickedInsideModal && 
                !event.target.closest('#webcam-container') &&
                !event.target.closest('#physical-container') &&
                !event.target.closest('.bg-gray-100')) {
                focusInput();
            }
        });
        // Initialize with physical scanner mode (QR input)
        switchToPhysicalMode();
        // Logout modal close button
        logoutClose.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
            justLoggedOut = false;
            logoutInProgress = false;
            location.reload();
        });

        // Logout confirmation modal buttons
        // Removed logout confirmation modal event listeners as per user request
    });

    // Add this helper function to append a row to the attendance table
    function addAttendanceRow(row) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.student_id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                <img src="${row.profile_picture ? asset('storage/' + row.profile_picture) : asset('images/default-profile.png')}"
                     alt="Profile Picture"
                     class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100 transition-transform duration-300 hover:scale-105" />
                <span class="font-medium">${row.student_name}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-${row.college}">${row.college}</span></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.activity}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.time_in}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.time_out}</td>
            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${row.status}</span></td>
        `;
        tableBody.prepend(tr);
    }
    </script>
</body>
</html> 