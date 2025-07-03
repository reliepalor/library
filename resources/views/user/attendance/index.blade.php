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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 space-y-8 transition-all duration-300 hover:shadow-xl">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-900">Today's Attendance</h2>
        </div>

        <!-- Attendance Logs -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300">
            <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Attendance Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto text-sm text-left text-gray-700">
                    <thead class="bg-blue-50 text-gray-600 uppercase text-xs font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 sm:px-6 py-4">Student ID</th>
                            <th class="px-4 sm:px-6 py-4">Name</th>
                            <th class="px-4 sm:px-6 py-4">College</th>
                            <th class="px-4 sm:px-6 py-4">Year</th>
                            <th class="px-4 sm:px-6 py-4">Activity</th>
                            <th class="px-4 sm:px-6 py-4">Login</th>
                            <th class="px-4 sm:px-6 py-4">Logout</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="attendance-table-body">
                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-blue-50 transition-all duration-200">
                                <td class="px-4 sm:px-6 py-4 font-medium text-gray-900">{{ $attendance->student_id }}</td>
                                <td class="px-4 sm:px-6 py-4 flex items-center space-x-3">
                                    <img src="{{ $attendance->student && $attendance->student->user && $attendance->student->user->profile_picture ? asset('storage/' . $attendance->student->user->profile_picture) : asset('images/default-profile.png') }}" 
                                         alt="Profile Picture" 
                                         class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100 transition-transform duration-300 hover:scale-105" />
                                    <span class="font-medium">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="college-{{ $attendance->student->college ?? '' }} bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $attendance->student->college ?? '' }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">{{ $attendance->student->year ?? '' }}</td>
                                <td class="px-4 sm:px-6 py-4">
                                    @if(str_contains($attendance->activity, 'Borrow'))
                                        @php
                                            $parts = explode(':', $attendance->activity);
                                                $activity = $parts[0];
                                                $bookCode = $parts[1] ?? 'N/A';
                                        @endphp
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $activity }}: {{ $bookCode }}</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $attendance->activity ?? '' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                                <td class="px-4 sm:px-6 py-4">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-6 text-center text-gray-500">No attendance logs yet.</td>
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
<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96 text-center">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Logout Confirmation</h3>
        <p class="mb-4 text-gray-700">You are currently logged in. Do you want to log out?</p>
        <div class="flex justify-center gap-4">
            <button id="logout-cancel" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg shadow-md transition duration-300">Cancel</button>
            <button id="logout-confirm" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md transition duration-300">Confirm</button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const activityModal = document.getElementById('activity-modal');
    const logoutModal = document.getElementById('logout-modal');
    const modalStudentId = document.getElementById('modal-student-id');
    const studentInfoDiv = document.getElementById('student-info');
    const activitySelect = document.getElementById('activity');
    const logoutConfirm = document.getElementById('logout-confirm');
    const logoutCancel = document.getElementById('logout-cancel');
    const modalCancel = document.getElementById('modal-cancel');
    const activityForm = document.getElementById('activity-form');
    // Add a simple QR input for demo (replace with webcam logic as needed)
    let qrInput = document.getElementById('qr-input');
    if (!qrInput) {
        qrInput = document.createElement('input');
        qrInput.type = 'text';
        qrInput.id = 'qr-input';
        qrInput.placeholder = 'Scan QR code here...';
        qrInput.className = 'border border-gray-300 p-3 w-full max-w-md mx-auto block rounded-lg my-6';
        document.body.insertBefore(qrInput, document.body.firstChild);
    }
    let isProcessing = false;
    let pendingLogout = null; // { studentId, activity }

    const handleQrScan = async (qrData) => {
        if (isProcessing) return;
        isProcessing = true;
        try {
            const cleanData = qrData.replace(/[^\x20-\x7E|]/g, '').trim();
            const parts = cleanData.split('|').map(part => part.trim());
            if (parts.length !== 4) throw new Error('Invalid QR code format.');
            const [studentId, name, college, yearRaw] = parts;
            if (!studentId) throw new Error('Student ID missing.');
            // Check for active session
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const checkResponse = await fetch(`/attendance/check?student_id=${encodeURIComponent(studentId)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (!checkResponse.ok) throw new Error('Server error.');
            const checkData = await checkResponse.json();
            if (checkData.hasActiveSession) {
                // Show logout confirmation modal, store pending logout info
                pendingLogout = { studentId, activity: checkData.activity || 'Logout' };
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
                <p class="text-sm text-gray-600">Year: ${yearRaw}</p>
            `;
            activitySelect.value = 'Study';
            activityModal.classList.remove('hidden');
        } catch (error) {
            alert(error.message);
            activityModal.classList.add('hidden');
            logoutModal.classList.add('hidden');
        } finally {
            isProcessing = false;
        }
    };
    // Listen for QR input (simulate scan)
    qrInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = qrInput.value.trim();
            if (value) {
                handleQrScan(value);
                qrInput.value = '';
            }
        }
    });
    // Activity form submission
    activityForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const studentId = modalStudentId.value;
        const activity = activitySelect.value;
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('/attendance/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
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
            activityModal.classList.add('hidden');
            // Optionally update the attendance table here
        } catch (error) {
            alert(error.message);
        }
    });
    // Logout modal confirm/cancel
    logoutConfirm.addEventListener('click', async () => {
        if (!pendingLogout) return;
        const { studentId, activity } = pendingLogout;
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('/attendance/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    student_id: studentId,
                    activity: activity
                }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Failed to log out');
            logoutModal.classList.add('hidden');
            alert('Logout time recorded successfully.');
        } catch (error) {
            alert(error.message);
        } finally {
            pendingLogout = null;
        }
    });
    logoutCancel.addEventListener('click', () => {
        logoutModal.classList.add('hidden');
        pendingLogout = null;
    });
    // Modal close buttons
    modalCancel.addEventListener('click', () => {
        activityModal.classList.add('hidden');
    });
});
</script>
</body>
</html>
