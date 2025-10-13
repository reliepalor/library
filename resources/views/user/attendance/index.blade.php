<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Study Area Script -->
    <script src="{{ asset('js/study-area.js') }}" defer></script>

    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles and Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Modern College Badge Styles */
        .college-CICS { 
            background-color: #c77dff;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CTED { 
            background-color: #90e0ef;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CCJE { 
            background-color: #ff4d6d; 
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CHM { 
            background-color: #ffc8dd; 
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CBEA { 
            background-color: #fae588;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CA { 
            background-color: #80ed99;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        /* Activity Badge */
        .activity-badge {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        /* Modern animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Card Styling */
        .modern-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }

        /* Table Styling */
        .modern-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .table-row {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f8fafc;
        }

        .table-row:hover {
            background: linear-gradient(135deg, #fefeff 0%, #f8fafc 100%);
            transform: translateY(-1px);
        }

        .table-row:last-child {
            border-bottom: none;
        }

        /* Profile Picture Enhancement */
        .profile-img {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            border: 2px solid white;
        }

        .profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Background */
        .main-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        /* Header Section */
        .header-section {
            background: transparent;
            border-radius: 24px;
            margin-bottom: 2rem;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .mobile-responsive {
                font-size: 0.875rem;
            }
            
            .mobile-stack > * {
                display: block;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body class="main-bg">
    <x-header />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <!-- Study Area Availability -->
        <div class="mb-6 fade-in">
            <div class="bg-white rounded-lg shadow-sm p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Study Area Availability</h3>
                        <p class="text-sm text-gray-500">Real-time updates on available study spaces</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span id="study-area-badge" class="badge bg-success text-white px-4 py-2 rounded-full text-sm font-medium">
                        <span id="study-area-availability">Loading...</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="header-section p-8 text-center text-white fade-in">
            <x-attendance-hero-section/>
        </div>
<!-- Study Area Availability -->
<div class="mb-6 fade-in">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start sm:items-center mb-4 sm:mb-0">
                <div class="p-3 rounded-full bg-blue-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Study Area Status</h3>
                    <p class="text-sm text-gray-500">
                        <span id="available-slots">-</span> of 30 slots available
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="hidden sm:flex items-center">
                    <div class="relative mr-3">
                        <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="progress-bar" class="h-full bg-green-500 transition-all duration-300" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div id="study-area-badge" class="flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-50 border border-gray-200">
                    <span class="w-2 h-2 rounded-full bg-gray-400 mr-2" id="status-dot"></span>
                    <span id="study-area-availability">Loading...</span>
                </div>
            </div>
        </div>
        
        <!-- Mobile progress bar -->
        <div class="mt-4 sm:hidden">
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div id="mobile-progress-bar" class="h-full bg-green-500 transition-all duration-300" style="width: 100%"></div>
            </div>
        </div>
    </div>
</div>

        <!-- Main Content -->
        <section class="modern-card p-8 fade-in">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-light text-gray-800 mb-2">Today's Attendance</h2>
                </div>
                <div class="flex items-center text-sm text-gray-600 mt-4 sm:mt-0">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></div>
                    Live Updates
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="modern-table bg-white overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-xl font-medium text-gray-800">Attendance Records</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">College</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Year</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Login</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Logout</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body">
                            @forelse($attendances as $attendance)
                                <tr class="table-row">
                                    <td class="px-6 py-4 font-semibold text-gray-800 text-sm">{{ $attendance->student_id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ \App\Services\AvatarService::getProfilePictureUrl($attendance->student->user->profile_picture ?? null, ($attendance->student->fname ?? '') . ' ' . ($attendance->student->lname ?? ''), 44) }}"
                                                alt="Profile Picture"
                                                class="profile-img"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'" />
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</div>
                                                <div class="text-sm text-gray-500 sm:hidden">{{ $attendance->student_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="college-{{ $attendance->student->college ?? '' }}">{{ $attendance->student->college ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 hidden sm:table-cell">{{ $attendance->student->year ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $activityText = $attendance->activity ?? '';
                                            $lower = strtolower($activityText);
                                        @endphp
                                        @if(str_contains($lower, 'wait for approval'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $activityText }}</span>
                                        @elseif(str_contains($lower, 'borrow:'))
                                            @php
                                                $parts = explode(':', $activityText);
                                                $code = $parts[1] ?? '';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Borrow: {{ trim($code) }}</span>
                                        @elseif(str_contains($lower, 'borrow book rejected'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $activityText }}</span>
                                        @elseif(str_contains($lower, 'book returned'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $activityText }}</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $activityText }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</div>
                                        <div class="text-xs text-gray-500 sm:hidden">Year: {{ $attendance->student->year ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</div>
                                        @if($attendance->logout)
                                            <div class="text-xs text-emerald-600">Completed</div>
                                        @else
                                            <div class="text-xs text-blue-600">Active</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-400">
                                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-lg font-medium">No attendance logs yet</p>
                                            <p class="text-sm">Student activities will appear here as they occur</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Teachers & Visitors Attendance Table -->
            <div class="modern-table bg-white overflow-hidden mt-8">
                <div class="flex flex-col sm:flex-row justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-xl font-medium text-gray-800">Teachers & Visitors Attendance</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Login</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Logout</th>
                            </tr>
                        </thead>
                        <tbody id="teacher-attendance-table-body">
                            @forelse($teacherAttendances as $attendance)
                                <tr class="table-row">
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 inline-flex items-center text-xs font-medium rounded bg-blue-100 text-blue-700">
                                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            {{ $attendance->teacherVisitor->role ?? 'Staff' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ \App\Services\AvatarService::getProfilePictureUrl($attendance->teacherVisitor->user->profile_picture ?? null, ($attendance->teacherVisitor->fname ?? '') . ' ' . ($attendance->teacherVisitor->lname ?? ''), 44) }}"
                                                alt="Profile Picture"
                                                class="profile-img"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'" />
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attendance->teacherVisitor->lname ?? '' }}, {{ $attendance->teacherVisitor->fname ?? '' }}</div>
                                                <div class="text-sm text-gray-500 sm:hidden">ID: {{ $attendance->teacher_visitor_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            {{ $attendance->teacherVisitor->department ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $activityText = $attendance->activity ?? '';
                                            $lower = strtolower($activityText);
                                        @endphp
                                        @if(str_contains($lower, 'wait for approval'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $activityText }}</span>
                                        @elseif(str_contains($lower, 'borrow:'))
                                            @php
                                                $parts = explode(':', $activityText);
                                                $code = $parts[1] ?? '';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Borrow: {{ trim($code) }}</span>
                                        @elseif(str_contains($lower, 'borrow book rejected'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $activityText }}</span>
                                        @elseif(str_contains($lower, 'book returned'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $activityText }}</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $activityText }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</div>
                                        @if($attendance->logout)
                                            <div class="text-xs text-emerald-600">Completed</div>
                                        @else
                                            <div class="text-xs text-blue-600">Active</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-400">
                                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-lg font-medium">No teacher attendance records yet</p>
                                            <p class="text-sm">Teacher activities will appear here as they occur</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="mb-20"></div>
    <x-footer />

    <script>
        // Add fade-in animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Realtime updates for user attendance tables (Students and Teachers)
        (function() {
            // Asset bases for images
            const storageBase = "{{ asset('storage') }}/";
            const defaultProfile = "{{ asset('images/default-profile.png') }}";

            // Format date time string to show only time (e.g., '09:05 AM')
            function formatDateTime(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'N/A';
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                } catch (e) {
                    console.error('Error formatting date:', e, 'Input was:', dateString);
                    return 'N/A';
                }
            }

            // Escape HTML function
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "<")
                    .replace(/>/g, ">")
                    .replace(/"/g, """)
                    .replace(/'/g, "&#039;");
            }

            const getActivityBadge = (text) => {
                if (!text) return '';
                const lower = String(text).toLowerCase();
                if (lower.includes('wait for approval')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">${escapeHtml(text)}</span>`;
                }
                if (lower.startsWith('borrow:')) {
                    const parts = String(text).split(':');
                    const code = (parts[1] || '').trim();
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Borrow: ${escapeHtml(code)}</span>`;
                }
                if (lower.includes('borrow book rejected')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">${escapeHtml(text)}</span>`;
                }
                if (lower.includes('book returned')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">${escapeHtml(text)}</span>`;
                }
                return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">${escapeHtml(text)}</span>`;
            };

            // Update student table
            function updateStudentTable(attendance) {
                const tbody = document.getElementById('attendance-table-body');
                if (!tbody) return;

                const attendanceData = Array.isArray(attendance) ? attendance : [];
                const fragment = document.createDocumentFragment();

                if (attendanceData.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-lg font-medium">No attendance logs yet</p>
                                <p class="text-sm">Student activities will appear here as they occur</p>
                            </div>
                        </td>
                    `;
                    fragment.appendChild(row);
                } else {
                    attendanceData.forEach((record) => {
                        if (!record) return;

                        const studentInfo = record.student || record;
                        const studentName = studentInfo.name || studentInfo.student_name || 'N/A';
                        const studentSection = studentInfo.section || studentInfo.student_section || studentInfo.section_name || '';
                        const studentCollege = studentInfo.college || studentInfo.student_college || studentInfo.college_name || '';
                        const studentCourse = studentInfo.course || studentInfo.student_course || studentInfo.course_name || '';
                        const studentId = record.student_id || record.id || '';

                        const timeInRaw = record.time_in || record.login || record.created_at || record.date;
                        const timeOutRaw = record.logout || record.time_out;

                        const timeIn = formatDateTime(timeInRaw);
                        const timeOut = timeOutRaw ? formatDateTime(timeOutRaw) : '';

                        let status = record.status;
                        if (!status) {
                            status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
                        }
                        const statusText = status === 'out' ? 'Signed Out' : 'Signed In';

                        const profilePic = record.profile_picture || defaultProfile;

                        const row = document.createElement('tr');
                        row.className = 'table-row';
                        row.innerHTML = `
                            <td class="px-6 py-4 font-semibold text-gray-800 text-sm" title="Student ID: ${studentId}">${studentId || '<span class="text-gray-400">N/A</span>'}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img class="profile-img" src="${profilePic}" alt="${escapeHtml(studentName)}" onerror="this.onerror=null; this.src='${defaultProfile}'; this.title='Default profile picture'" />
                                    <div>
                                        <div class="font-medium text-gray-900">${escapeHtml(studentName)}</div>
                                        ${studentSection ? `<div class="text-xs text-gray-500">${escapeHtml(studentSection)}</div>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                ${studentCollege ? `<span class="college-${studentCollege}">${escapeHtml(studentCollege)}</span>` : '<span class="text-gray-400">N/A</span>'}
                                ${studentCourse ? `<div class="mt-1 text-xs text-gray-500">${escapeHtml(studentCourse)}</div>` : ''}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 hidden sm:table-cell">${studentCourse || ''}</td>
                            <td class="px-6 py-4">${getActivityBadge(record.activity)}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeIn}</div>
                                <div class="text-xs text-gray-500 sm:hidden">Year: ${studentCourse || ''}</div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <div class="text-sm font-medium text-gray-900">${timeOut}</div>
                                <div class="text-xs ${status === 'out' ? 'text-emerald-600' : 'text-blue-600'}">${statusText}</div>
                            </td>
                        `;
                        fragment.appendChild(row);
                    });
                }

                tbody.innerHTML = '';
                tbody.appendChild(fragment);
            }

            // Update teacher table
            function updateTeacherTable(attendance) {
                const tbody = document.getElementById('teacher-attendance-table-body');
                if (!tbody) return;

                const attendanceData = Array.isArray(attendance) ? attendance : [];
                const fragment = document.createDocumentFragment();

                if (attendanceData.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-lg font-medium">No teacher attendance records yet</p>
                                <p class="text-sm">Teacher activities will appear here as they occur</p>
                            </div>
                        </td>
                    `;
                    fragment.appendChild(row);
                } else {
                    attendanceData.forEach((record) => {
                        if (!record) return;

                        // Use the exact field names from the User controller response
                        const teacherName = record.name || 'N/A';
                        const teacherRole = record.role || 'Staff';
                        const teacherDepartment = record.department || 'N/A';
                        const teacherId = record.teacher_visitor_id || record.id || '';

                        const timeInRaw = record.time_in || record.login || record.created_at;
                        const timeOutRaw = record.time_out || record.logout;

                        const timeIn = formatDateTime(timeInRaw);
                        const timeOut = timeOutRaw ? formatDateTime(timeOutRaw) : '';

                        let status = record.status;
                        if (!status) {
                            status = (timeOutRaw || record.time_out || record.logout) ? 'out' : 'in';
                        }
                        const statusText = status === 'out' ? 'Signed Out' : 'Signed In';

                        const profilePic = record.profile_picture || defaultProfile;

                        const row = document.createElement('tr');
                        row.className = 'table-row';
                        row.innerHTML = `
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex items-center text-xs font-medium rounded bg-blue-100 text-blue-700">
                                    <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    ${escapeHtml(teacherRole)}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img class="profile-img" src="${profilePic}" alt="${escapeHtml(teacherName)}" onerror="this.onerror=null; this.src='${defaultProfile}'; this.title='Default profile picture'" />
                                    <div>
                                        <div class="font-medium text-gray-900">${escapeHtml(teacherName)}</div>
                                        <div class="text-xs text-gray-500">ID: ${escapeHtml(teacherId)}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    ${escapeHtml(teacherDepartment)}
                                </span>
                            </td>
                            <td class="px-6 py-4">${getActivityBadge(record.activity)}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeIn}</div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <div class="text-sm font-medium text-gray-900">${timeOut}</div>
                                <div class="text-xs ${status === 'out' ? 'text-emerald-600' : 'text-blue-600'}">${statusText}</div>
                            </td>
                        `;
                        fragment.appendChild(row);
                    });
                }

                tbody.innerHTML = '';
                tbody.appendChild(fragment);
            }

            let inFlight = null;
            const refreshAttendanceTable = async () => {
                try {
                    if (inFlight) return;
                    inFlight = true;

                    const res = await fetch('{{ route('user.attendance.realtime') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json?.success || !json?.data) return;

                    const responseData = json.data || json;
                    let studentData = [];
                    let teacherData = [];

                    // Handle the structured response format from user controller
                    if (typeof responseData === 'object' && responseData.studentAttendance && responseData.teacherAttendance) {
                        // Data is already separated in user format
                        studentData = Array.isArray(responseData.studentAttendance) ? responseData.studentAttendance : [];
                        teacherData = Array.isArray(responseData.teacherAttendance) ? responseData.teacherAttendance : [];
                    } else if (Array.isArray(responseData)) {
                        // Handle mixed array format - filter based on available fields
                        responseData.forEach(item => {
                            if (!item) return;

                            // Check for teacher/visitor indicators
                            const isTeacher = item.user_type === 'teacher' ||
                                            item.teacher_id ||
                                            item.teacher_visitor_id ||
                                            item.role === 'teacher' ||
                                            item.type === 'teacher' ||
                                            item.teacher_name ||
                                            (item.role && item.role !== 'student') ||
                                            (item.college_or_dept && !item.student_id && !item.identifier?.match(/^\d{4}-\d{4}$/));

                            if (isTeacher) {
                                teacherData.push(item);
                            } else {
                                // Assume student if not identified as teacher
                                studentData.push(item);
                            }
                        });
                    } else if (typeof responseData === 'object') {
                        // Fallback for other object formats
                        studentData = Array.isArray(responseData.studentAttendance || responseData.students) ?
                            (responseData.studentAttendance || responseData.students) : [];
                        teacherData = Array.isArray(responseData.teacherAttendance || responseData.teachers) ?
                            (responseData.teacherAttendance || responseData.teachers) : [];
                    }

                    // Remove any null/undefined items
                    studentData = studentData.filter(Boolean);
                    teacherData = teacherData.filter(Boolean);

                    updateStudentTable(studentData);
                    updateTeacherTable(teacherData);

                } catch (e) {
                    console.debug('User realtime fetch failed:', e);
                } finally {
                    inFlight = false;
                }
            };

            // Initial load and poll every 3s
            refreshAttendanceTable();
            setInterval(refreshAttendanceTable, 3000);
        })();
    </script>
</body>
</html>