<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library  </title>
      <link rel="icon" type="image/x-icon" href="/images/library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/heroicons@2.0.16/dist/20/outline.min.js"></script>

    <!-- Styles and Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Add this in the head section after the existing styles -->
    <style>
        .college-CICS { 
            background-color: #c77dff;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CTED { 
            background-color: #90e0ef;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CCJE { 
            background-color: #ff4d6d;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CHM { 
            background-color: #ffc8dd;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CBEA { 
            background-color: #fae588;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CA { 
            background-color: #80ed99;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>
    <x-header />

    <div class="flex justify-center px-6 border border-gray-200 shadow-sm py-8 bg-white rounded-lg max-w-7xl mx-auto space-y-6 mt-20">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">QR Attendance Logging</h2>
                <button 
                    onclick="toggleFullScreen()" 
                    class="px-3 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-xl text-md shadow-lg"
                >
                    Full Screen
                </button>
            </div>

            <!-- Mode Toggle Buttons -->
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

            <!-- QR Scanner Display (Webcam Mode) -->
            <div id="webcam-container" class="mb-4">
                <div id="qr-reader" class="my-4 mx-auto" style="width: 500px;"></div>
            </div>

            <!-- QR Input for Physical Scanner -->
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

            <!-- Status Display -->
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
            <div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-96 text-center">
                    <h3 class="text-lg font-semibold mb-4">Logout Successful</h3>
                    <p class="mb-4">You have been logged out successfully.</p>
                    <button id="logout-close" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
                </div>
            </div>

            <!-- Attendance Logs -->
            <div class="mt-10 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between items-center p-6 bg-gray-100 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Today's Attendance</h3>
                </div>
                <div class="overflow-x-auto p-4">
                    <table class="w-full table-auto text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b">
                            <tr>
                                <th class="px-6 py-3">Student ID</th>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">College</th>
                                <th class="px-6 py-3">Year</th>
                                <th class="px-6 py-3">Activity</th>
                                <th class="px-6 py-3">Login</th>
                                <th class="px-6 py-3">Logout</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="attendance-table-body">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $attendance->student_id }}</td>
                                    <td class="px-6 py-4">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="college-{{ $attendance->student->college ?? '' }}">{{ $attendance->student->college ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">{{ $attendance->student->year ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        @if(str_contains($attendance->activity, 'Borrow'))
                                            @php
                                                $parts = explode(':', $attendance->activity);
                                                $activity = $parts[0];
                                                $bookCode = $parts[1] ?? 'N/A';
                                            @endphp
                                            {{ $activity }}: {{ $bookCode }}
                                        @else
                                            {{ $attendance->activity ?? '' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                                    <td class="px-6 py-4">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No attendance logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-20"></div>
        <x-footer />

    <!-- html5-qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

    <!-- Main JavaScript -->
    <script>
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
            const downloadLogButton = document.getElementById('download-log');
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
            let attendanceLogs = [];
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
                console.log('Status:', message);
                
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
                // Fade out physical, fade in webcam
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
                // Fade out webcam, fade in physical
                webcamContainer.classList.add('opacity-0');
                setTimeout(() => {
                    webcamContainer.classList.add('hidden');
                    physicalContainer.classList.remove('hidden');
                    setTimeout(() => {
                        physicalContainer.classList.remove('opacity-0');
                        // Clear input and focus
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
                            console.log('Webcam QR Scanned:', decodedText);
                            handleQrScan(decodedText);
                        },
                        (error) => {
                            // Suppress frequent scan errors in console
                        }
                    ).then(() => {
                        scannerRunning = true;
                        console.log('Webcam scanner started successfully');
                    }).catch(err => {
                        console.warn('Failed to start webcam scanner:', err);
                        qrReader.innerHTML = '<p class="text-orange-600 text-center">Camera access failed. Please allow camera access or use physical scanner.</p>';
                        scannerRunning = false;
                    });
                } catch (err) {
                    console.error('Webcam initialization error:', err);
                    qrReader.innerHTML = '<p class="text-red-600 text-center">Camera initialization failed. Use physical scanner instead.</p>';
                    scannerRunning = false;
                }
            };

            // Stop webcam scanner
            const stopWebcamScanner = () => {
                if (html5QrCode && scannerRunning) {
                    try {
                        html5QrCode.stop().then(() => {
                            console.log('Webcam scanner stopped');
                            scannerRunning = false;
                        }).catch(err => {
                            console.warn('Error stopping webcam scanner:', err);
                            scannerRunning = false;
                        });
                    } catch (err) {
                        console.warn('Error stopping webcam scanner:', err);
                        scannerRunning = false;
                    }
                }
            };

            // QR Input event listeners (Physical Scanner Mode)
            let inputTimeout;
            let accumulatedData = ''; // Add this to store accumulated data
            let qrParts = []; // Add this to store QR code parts

            qrInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                console.log('Input value:', value);
                
                // Clear any existing timeout
                if (inputTimeout) {
                    clearTimeout(inputTimeout);
                }

                // If we have a complete QR code (contains |)
                if (value.includes('|')) {
                    console.log('Complete QR part detected:', value);
                    // Add the part to our array
                    qrParts.push(value.replace('|', '').trim());
                    
                    // If we have all 4 parts, process the complete QR code
                    if (qrParts.length === 4) {
                        const completeQR = qrParts.join('|');
                        console.log('Complete QR code assembled:', completeQR);
                        handleQrScan(completeQR);
                        qrInput.value = '';
                        qrParts = [];
                        return;
                    }
                    
                    // Clear input for next part
                    qrInput.value = '';
                    return;
                }

                // If we have a value but no | yet, wait a bit to see if more data comes in
                if (value) {
                    inputTimeout = setTimeout(() => {
                        if (value === qrInput.value.trim()) {
                            console.log('Processing final value:', value);
                            qrParts.push(value.trim());
                            
                            // If we have all 4 parts, process the complete QR code
                            if (qrParts.length === 4) {
                                const completeQR = qrParts.join('|');
                                console.log('Complete QR code assembled:', completeQR);
                                handleQrScan(completeQR);
                                qrInput.value = '';
                                qrParts = [];
                            }
                        }
                    }, 50); // Reduced timeout for faster response
                }
            });

            // Add keydown event listener for Enter key
            qrInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = qrInput.value.trim();
                    if (value) {
                        console.log('Enter key pressed, processing:', value);
                        qrParts.push(value.trim());
                        
                        // If we have all 4 parts, process the complete QR code
                        if (qrParts.length === 4) {
                            const completeQR = qrParts.join('|');
                            console.log('Complete QR code assembled:', completeQR);
                            handleQrScan(completeQR);
                            qrInput.value = '';
                            qrParts = [];
                        }
                    }
                }
            });

            // Add paste event listener for manual pasting
            qrInput.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                if (pastedText) {
                    console.log('Paste detected, processing:', pastedText);
                    handleQrScan(pastedText.trim());
                    qrInput.value = '';
                    qrParts = [];
                }
            });

            // Add focus event listener to clear input on focus
            qrInput.addEventListener('focus', () => {
                if (currentMode === 'physical') {
                    qrInput.value = '';
                    qrParts = [];
                }
            });

            // Add blur event listener to handle incomplete scans
            qrInput.addEventListener('blur', () => {
                const value = qrInput.value.trim();
                if (value && currentMode === 'physical' && !isProcessing) {
                    console.log('Physical scanner detected QR code (blur):', value);
                    qrParts.push(value.trim());
                    
                    // If we have all 4 parts, process the complete QR code
                    if (qrParts.length === 4) {
                        const completeQR = qrParts.join('|');
                        console.log('Complete QR code assembled:', completeQR);
                        handleQrScan(completeQR);
                        qrInput.value = '';
                        qrParts = [];
                    }
                }
            });

            // Update handleQrScan to use the new processQRCode function
            const handleQrScan = async (qrData) => {
                console.log('handleQrScan called with:', qrData);
                if (isProcessing || logoutInProgress) {
                    console.log('Already processing or logout in progress');
                    return;
                }

                isProcessing = true;
                showStatus('Processing QR code...');

                try {
                    // Clean the QR data - remove any extra characters and normalize the format
                    const cleanData = qrData
                        .replace(/[\r\n]+/g, '') // Remove line breaks
                        .replace(/[^\w\s\-|]/g, '') // Keep only alphanumeric, spaces, hyphens, and pipes
                        .trim();
                    
                    console.log('Cleaned QR data:', cleanData);

                    // Parse QR data
                    const parts = cleanData.split('|').map(part => part.trim());
                    console.log('Parsed QR data parts:', parts);
                    console.log('Number of parts:', parts.length);

                    // Validate the parts
                    if (parts.length !== 4) {
                        console.error('Invalid parts length:', parts.length);
                        console.error('Parts:', parts);
                        throw new Error('Invalid QR code format. Expected student ID, name, college, and year.');
                    }

                    // Validate each part
                    const [studentId, name, college, year] = parts;
                    
                    if (!studentId) {
                        throw new Error('Student ID is missing or invalid.');
                    }
                    if (!name) {
                        throw new Error('Student name is missing or invalid.');
                    }
                    if (!college) {
                        throw new Error('College is missing or invalid.');
                    }
                    if (!year) {
                        throw new Error('Year is missing or invalid.');
                    }

                    console.log('Validated parts:', {
                        studentId,
                        name,
                        college,
                        year
                    });

                    // Get CSRF token
                    const csrfToken = getCSRFToken();
                    if (!csrfToken) {
                        throw new Error('CSRF token not found. Please refresh the page.');
                    }

                    // First, fetch student data to validate
                    console.log('Fetching student data...');
                    const response = await fetch(`/user/attendance/scan?student_id=${encodeURIComponent(studentId)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('Student data response:', data);

                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (!data.students) {
                        throw new Error('Student not found in database');
                    }

                    // Check for active session
                    console.log('Checking for active session...');
                    const checkResponse = await fetch(`/user/attendance/check?student_id=${encodeURIComponent(studentId)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!checkResponse.ok) {
                        throw new Error(`Server error: ${checkResponse.status}`);
                    }

                    const checkData = await checkResponse.json();
                    console.log('Active session check result:', checkData);

                    if (checkData.hasActiveSession) {
                        // Handle logout
                        console.log('Student has active session, processing logout...');
                        activityModal.classList.add('hidden');
                        borrowModal.classList.add('hidden');
                        otherModal.classList.add('hidden');
                        logoutInProgress = true;

                        const logoutResponse = await fetch('/user/attendance/log', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ student_id: studentId, activity: checkData.activity }),
                        });

                        if (!logoutResponse.ok) {
                            throw new Error(`Server error: ${logoutResponse.status}`);
                        }

                        const logoutData = await logoutResponse.json();
                        showStatus(logoutData.message || 'Logout recorded successfully!', 'success');
                        logoutModal.classList.remove('hidden');

                        setTimeout(() => {
                            logoutModal.classList.add('hidden');
                            logoutInProgress = false;
                            location.reload();
                        }, 2000);
                        return;
                    }

                    // Set up modal for login
                    console.log('Setting up activity modal...');
                    modalStudentId.value = studentId;
                    
                    // Extract name parts
                    const nameParts = name.split(' ');
                    const fname = nameParts[0] || '';
                    const lname = nameParts.slice(1).join(' ') || '';
                    
                    // Update student info in modal
                    studentInfoDiv.innerHTML = `
                        <p class="font-medium text-gray-800">${lname}, ${fname}</p>
                        <p class="text-sm text-gray-600">ID: ${studentId}</p>
                        <p class="text-sm text-gray-600">College: ${college}</p>
                        <p class="text-sm text-gray-600">Year: ${year}</p>
                    `;
                    
                    activitySelect.value = 'Study';
                    
                    // Show the activity modal
                    console.log('Showing activity modal...');
                    activityModal.classList.remove('hidden');
                    showStatus('Student found! Please select activity for login.', 'success');

                } catch (error) {
                    console.error('QR processing error:', error);
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
                console.log('Activity selected:', selectedActivity);
                
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

                    const response = await fetch('/user/attendance/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken(),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ 
                            student_id: studentId, 
                            activity: activity 
                        }),
                    });

                    const data = await response.json();

                    console.log('Attendance log response:', data); // Added debug log

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to log attendance');
                    }

                    showStatus(data.message || 'Attendance logged successfully!', 'success');

                    // Close modal and reset
                    activityModal.classList.add('hidden');
                    
                    // Refresh page
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Attendance logging error:', error);
                    alert('Attendance logging error: ' + error.message); // Added alert for debugging
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

                    // First check if book can be borrowed
                    const borrowResponse = await fetch('/user/borrow/request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken(),
                            'Accept': 'application/json',
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
                    const attendanceResponse = await fetch('/user/attendance/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken(),
                            'Accept': 'application/json',
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

                    // Close modal and reset
                    borrowModal.classList.add('hidden');
                    document.getElementById('book_id').value = '';
                    
                    // Refresh page
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Borrow request error:', error);
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

                    const response = await fetch('/user/attendance/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken(),
                            'Accept': 'application/json',
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

                    // Close modal and reset
                    otherModal.classList.add('hidden');
                    document.getElementById('custom_activity').value = '';
                    
                    // Refresh page
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Other activity error:', error);
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

            // Handle session student ID (if coming from redirect)
            @if(session('modal_student_id'))
                modalStudentId.value = "{{ session('modal_student_id') }}";
                fetch(`/user/attendance/check?student_id={{ session('modal_student_id') }}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }
                    let checkData;
                    try {
                        checkData = await response.json();
                    } catch (e) {
                        throw new Error('Invalid JSON response from server');
                    }
                    if (checkData.hasActiveSession) {
                        // Student is already logged in, trigger logout
                        logoutInProgress = true;
                        let logoutResponse, logoutData;
                        logoutResponse = await fetch('/user/attendance/log', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': getCSRFToken(),
                            },
                            body: JSON.stringify({ student_id: "{{ session('modal_student_id') }}" }),
                        });
                        if (logoutResponse.ok) {
                            try {
                                logoutData = await logoutResponse.json();
                                showStatus(logoutData.message || 'Logout recorded successfully!', 'success');
                            } catch (e) {
                                showStatus('Logout recorded, but invalid JSON response from server', 'warning');
                            }
                        } else {
                            showStatus('Failed to record logout', 'error');
                        }
                        logoutModal.classList.remove('hidden');
                        setTimeout(() => {
                            logoutModal.classList.add('hidden');
                            logoutInProgress = false;
                            location.reload();
                        }, 2000);
                    } else {
                        // Student is not logged in, show activity modal
                        fetch(`/user/attendance/scan?student_id={{ session('modal_student_id') }}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': getCSRFToken()
                            }
                        })
                        .then(async response => {
                            if (!response.ok) {
                                throw new Error('Server error: ' + response.status);
                            }
                            let data;
                            try {
                                data = await response.json();
                            } catch (e) {
                                throw new Error('Invalid JSON response from server');
                            }
                            if (data.students) {
                                const student = data.students;
                                studentInfoDiv.innerHTML = `
                                    <p class=\"font-medium text-gray-800\">${student.lname}, ${student.fname}</p>
                                    <p class=\"text-sm text-gray-600\">ID: ${student.student_id}</p>
                                    <p class=\"text-sm text-gray-600\">College: ${student.college || 'N/A'}</p>
                                    <p class=\"text-sm text-gray-600\">Year: ${student.year || 'N/A'}</p>
                                `;
                                activityModal.classList.remove('hidden');
                                showStatus('Student loaded from session');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching session student info:', error);
                            showStatus('Error loading student from session', 'error');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking session student active status:', error);
                    showStatus('Error checking student session status', 'error');
                });
            @endif

            // Logout modal close button
            logoutClose.addEventListener('click', () => {
                logoutModal.classList.add('hidden');
                justLoggedOut = false;
                logoutInProgress = false;
                location.reload();
            });

            console.log('QR Attendance System initialized successfully');
        });
    </script>
</body>
</html>