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
            background-color: #e9d5ff;
            color: #454545;
          padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CTED { 
            background-color: #bfdbfe;
            color: #454545;
          padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CCJE { 
            background-color: #fecaca; 
            color: #454545;
          padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CHM { 
            background-color: #fbcfe8; 
            color: #454545;
          padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CBEA { 
            background-color: #fef9c3;
            color: #454545;
            padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .college-CA { 
            background-color: #bbf7d0;
            color: #454545;
          padding: 0.300rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
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
            overflow: visible;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
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

            /* Enhanced responsive table for small screens */
            .table-container {
                -webkit-overflow-scrolling: touch;
            }

            .table-container th,
            .table-container td {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .profile-img {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }

            /* Abbreviate long content on mobile */
            .activity-tag span {
                font-size: 0.7rem;
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .college-badge {
                font-size: 0.65rem;
                padding: 0.2rem 0.3rem;
            }
        }

        @media (max-width: 480px) {
            .table-container th,
            .table-container td {
                padding: 0.25rem 0.125rem;
                font-size: 0.7rem;
            }

            .profile-img {
                width: 28px;
                height: 28px;
            }
        }

        /* Filter dropdown styles */
        .filter-dropdown {
            transform-origin: top;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Add class for college badges to make them responsive */
        .college-badge {
            padding: 0.3rem 0.45rem;
            font-size: 0.72rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="main-bg">
    <x-header />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">


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
                            <span id="available-slots">-</span> of <span id="max-slots">30</span> slots available
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
                        <span id="study-area-availability"></span>
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
        <div class="modern-table bg-white relative">
            <div class="bg-purple-50 flex flex-col sm:flex-row justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-medium text-gray-800">Students Attendance</h3>
            </div>



            <!-- Filter Dropdowns Container (positioned relative to table) -->
            <div id="college-dropdown" class="fixed bg-white border border-gray-200 rounded-md shadow-lg z-50 hidden opacity-0 scale-y-95 transition-all duration-200 w-32">
                <div class="py-1">
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="All">All</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CICS">CICS</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CTED">CTED</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CCJE">CCJE</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CHM">CHM</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CBEA">CBEA</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="college" data-value="CA">CA</button>
                </div>
            </div>
            <div id="year-dropdown" class="fixed bg-white border border-gray-200 rounded-md shadow-lg z-50 hidden opacity-0 scale-y-95 transition-all duration-200 w-24">
                <div class="py-1">
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="year" data-value="All">All</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="year" data-value="1">1st</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="year" data-value="2">2nd</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="year" data-value="3">3rd</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="year" data-value="4">4th</button>
                </div>
            </div>
            <div id="activity-dropdown" class="fixed bg-white border border-gray-200 rounded-md shadow-lg z-50 hidden opacity-0 scale-y-95 transition-all duration-200 w-40">
                <div class="py-1">
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="activity" data-value="All">All</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="activity" data-value="Stay to Study">Stay to Study</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="activity" data-value="Borrow">Borrow</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="activity" data-value="Stay&Borrow">Stay&Borrow</button>
                    <button class="block w-full text-left px-3 py-2 text-xs hover:bg-gray-100 transition-colors duration-150 filter-option" data-filter="activity" data-value="Book Returned">Book Returned</button>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="block">
                <div class="overflow-x-auto relative table-container">
                    <div class="max-h-[400px] overflow-auto">
                        <table class="w-full">
                            <thead class="table-header sticky top-0 bg-white z-10">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <button id="college-filter-btn" class="flex items-center space-x-1 hover:bg-gray-50 px-2 py-1 rounded transition-colors duration-150">
                                            <span>College</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <button id="year-filter-btn" class="flex items-center space-x-1 hover:bg-gray-50 px-2 py-1 rounded transition-colors duration-150">
                                            <span>Year</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <button id="activity-filter-btn" class="flex items-center space-x-1 hover:bg-gray-50 px-2 py-1 rounded transition-colors duration-150">
                                            <span>Activity</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Login</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Logout</th>
                                </tr>
                            </thead>
                            <tbody id="attendance-table-body">
                            @forelse($attendances as $attendance)
                                <tr class="table-row attendance-row" data-college="{{ $attendance->student->college ?? '' }}" data-year="{{ $attendance->student->year ?? '' }}" data-activity="{{ $attendance->activity ?? '' }}">
                                    <td class="px-6 py-4 font-semibold text-gray-800 text-sm">{{ $attendance->student_id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ \App\Services\AvatarService::getProfilePictureUrl($attendance->student->user->profile_picture ?? null, ($attendance->student->fname ?? '') . ' ' . ($attendance->student->lname ?? ''), 44) }}"
                                                alt="Profile Picture"
                                                class="profile-img"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'" />
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="college-{{ $attendance->student->college ?? '' }} college-badge">{{ $attendance->student->college ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->student->year ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $activityText = $attendance->activity ?? '';
                                            $lower = strtolower($activityText);
                                        @endphp
                                        @if(str_contains($lower, 'wait for approval'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 activity-tag">{{ $activityText }}</span>
                                        @elseif(str_starts_with($lower, 'stay&borrow:'))
                                            @php
                                                $parts = explode(':', $activityText);
                                                $code = $parts[1] ?? '';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Stay&Borrow: {{ trim($code) }}</span>
                                        @elseif(str_contains($lower, 'borrow:'))
                                            @php
                                                $parts = explode(':', $activityText);
                                                $code = $parts[1] ?? '';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Borrow: {{ trim($code) }}</span>
                                        @elseif(str_contains($lower, 'borrow book rejected'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 activity-tag">{{ $activityText }}</span>
                                        @elseif(str_contains($lower, 'book returned'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 activity-tag">{{ $activityText }}</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-gray-800 activity-tag">{{ $activityText }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</div>
                                        @if($attendance->logout)
                                            <div class="text-xs text-emerald-600 hidden">Completed</div>
                                        @else
                                            <div class="text-xs text-blue-600 hidden">Active</div>
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
        </div>

        <!-- Teachers & Visitors Attendance Table -->
        <div class="modern-table bg-white overflow-hidden mt-8">
            <div class="bg-blue-50 flex flex-col sm:flex-row justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-medium text-gray-800">Teachers & Visitors Attendance</h3>
            </div>

            <!-- Responsive Table -->
             <div class="block">
                 <div class="overflow-x-auto table-container">
                     <div class="max-h-[400px] overflow-auto">
                         <table class="w-full">
                             <thead class="table-header sticky top-0 bg-white z-10">
                                 <tr>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Activity</th>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Login</th>
                                     <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Logout</th>
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
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 activity-tag">{{ $activityText }}</span>
                                             @elseif(str_starts_with($lower, 'stay&borrow:'))
                                                 @php
                                                     $parts = explode(':', $activityText);
                                                     $code = $parts[1] ?? '';
                                                 @endphp
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Stay&Borrow: {{ trim($code) }}</span>
                                             @elseif(str_contains($lower, 'borrow:'))
                                                 @php
                                                     $parts = explode(':', $activityText);
                                                     $code = $parts[1] ?? '';
                                                 @endphp
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Borrow: {{ trim($code) }}</span>
                                             @elseif(str_contains($lower, 'borrow book rejected'))
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 activity-tag">{{ $activityText }}</span>
                                             @elseif(str_contains($lower, 'book returned'))
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 activity-tag">{{ $activityText }}</span>
                                             @elseif(str_contains($lower, 'study'))
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 activity-tag">Stay to Study</span>
                                             @else
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 activity-tag">{{ $activityText }}</span>
                                             @endif
                                         </td>
                                         <td class="px-6 py-4">
                                             <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</div>
                                         </td>
                                         <td class="px-6 py-4">
                                             <div class="text-sm font-medium text-gray-900">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</div>
                                             @if($attendance->logout)
                                                 <div class="text-xs text-emerald-600 hidden">Completed</div>
                                             @else
                                                 <div class="text-xs text-blue-600 hidden">Active</div>
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
             </div>
        </div>
        </section>
    </div>

    <div class="mb-20"></div>
    <x-footer />

    <x-library-policy />

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

            const getActivityTag = (text) => {
                if (!text) return '';
                const lower = String(text).toLowerCase();
                if (lower.includes('wait for approval')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 activity-tag">${escapeHtml(text)}</span>`;
                }
                if (lower.startsWith('stay&borrow:')) {
                    const parts = String(text).split(':');
                    const code = (parts[1] || '').trim();
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Stay&Borrow: ${escapeHtml(code)}</span>`;
                }
                if (lower.startsWith('borrow:')) {
                    const parts = String(text).split(':');
                    const code = (parts[1] || '').trim();
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 activity-tag">Borrow: ${escapeHtml(code)}</span>`;
                }
                if (lower.includes('borrow book rejected')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 activity-tag">${escapeHtml(text)}</span>`;
                }
                if (lower.includes('book returned')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 activity-tag">${escapeHtml(text)}</span>`;
                }
                if (lower.includes('study')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 activity-tag">Stay to Study</span>`;
                }
                return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 activity-tag">${escapeHtml(text)}</span>`;
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

                        const studentName = record.student_name || 'N/A';
                        const studentCollege = record.college || '';
                        const studentYear = record.year || '';
                        const studentId = record.student_id || '';

                        const timeIn = record.time_in || '';
                        const timeOut = record.time_out || '';

                        const profilePic = record.profile_picture || defaultProfile;

                        const row = document.createElement('tr');
                        row.className = 'table-row attendance-row';
                        row.dataset.college = studentCollege;
                        row.dataset.year = studentYear;
                        row.dataset.activity = record.activity || '';
                        row.innerHTML = `
                            <td class="px-6 py-4 font-semibold text-gray-800 text-sm" title="Student ID: ${studentId}">${studentId || '<span class="text-gray-400">N/A</span>'}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img class="profile-img" src="${profilePic}" alt="${escapeHtml(studentName)}" onerror="this.onerror=null; this.src='${defaultProfile}'; this.title='Default profile picture'" />
                                    <div>
                                        <div class="font-medium text-gray-900">${escapeHtml(studentName)}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                ${studentCollege ? `<span class="college-${studentCollege} college-badge">${escapeHtml(studentCollege)}</span>` : '<span class="text-gray-400">N/A</span>'}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">${studentYear || ''}</td>
                            <td class="px-6 py-4">${getActivityTag(record.activity)}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeIn}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeOut}</div>
                                <div class="text-xs ${timeOut ? 'text-emerald-600' : 'text-blue-600'}">${timeOut ? 'Completed' : 'Active'}</div>
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

                        const teacherName = record.name || 'N/A';
                        const teacherRole = record.role || 'Staff';
                        const teacherDepartment = record.department || 'N/A';
                        const teacherId = record.teacher_visitor_id || '';

                        const timeIn = record.time_in || '';
                        const timeOut = record.time_out || '';

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
                            <td class="px-6 py-4">${getActivityTag(record.activity)}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeIn}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${timeOut}</div>
                                <div class="text-xs ${timeOut ? 'text-emerald-600' : 'text-blue-600'}">${timeOut ? 'Completed' : 'Active'}</div>
                            </td>
                        `;
                        fragment.appendChild(row);
                    });
                }

                tbody.innerHTML = '';
                tbody.appendChild(fragment);
            }

            let inFlight = null;
            let lastUpdateTime = null;

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
                    const currentUpdateTime = responseData.last_updated;

                    // Only update if we have new data
                    if (lastUpdateTime && currentUpdateTime === lastUpdateTime) {
                        return; // No new data, skip update
                    }
                    lastUpdateTime = currentUpdateTime;

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

                    // Smooth update with fade effect
                    const tables = document.querySelectorAll('#attendance-table-body, #teacher-attendance-table-body');
                    tables.forEach(table => {
                        table.style.opacity = '0.7';
                        table.style.transition = 'opacity 0.3s ease';
                    });

                    console.log('Updating tables with student data:', studentData.length, 'teacher data:', teacherData.length);
                    updateStudentTable(studentData);
                    updateTeacherTable(teacherData);

                    // Fade back in
                    setTimeout(() => {
                        tables.forEach(table => {
                            table.style.opacity = '1';
                        });
                    }, 100);

                } catch (e) {
                    console.debug('User realtime fetch failed:', e);
                } finally {
                    inFlight = false;
                }
            };

            // Initial load and poll every 2 seconds for smoother updates
            console.log('Starting initial load and polling...');
            refreshAttendanceTable();
            setInterval(refreshAttendanceTable, 2000);
        })();

        // Attendance Filter Functionality
        (function() {
            console.log('Attendance filter script loaded');

            let currentFilters = {
                college: 'All',
                year: 'All',
                activity: 'All'
            };

            // Helper function to get activity category
            function getActivityCategory(activity) {
                if (!activity) return '';
                const lower = activity.toLowerCase();
                if (lower.includes('wait for approval')) return 'Wait for Approval';
                if (lower.startsWith('stay&borrow:')) return 'Stay&Borrow';
                if (lower.startsWith('borrow:')) return 'Borrow';
                if (lower.includes('borrow book rejected')) return 'Borrow Book Rejected';
                if (lower.includes('book returned')) return 'Book Returned';
                if (lower.includes('study')) return 'Stay to Study';
                return activity;
            }

            // Filter rows based on current filters
            function applyFilters() {
                const rows = document.querySelectorAll('.attendance-row');
                rows.forEach(row => {
                    const college = row.dataset.college || '';
                    const year = row.dataset.year || '';
                    const activity = getActivityCategory(row.dataset.activity || '');

                    const collegeMatch = currentFilters.college === 'All' || college === currentFilters.college;
                    const yearMatch = currentFilters.year === 'All' || year === currentFilters.year.toString();
                    const activityMatch = currentFilters.activity === 'All' || activity === currentFilters.activity;

                    if (collegeMatch && yearMatch && activityMatch) {
                        row.style.display = '';
                        row.classList.remove('hidden');
                    } else {
                        row.style.display = 'none';
                        row.classList.add('hidden');
                    }
                });
            }

            // Toggle dropdown visibility
            function toggleDropdown(dropdownId, buttonId) {
                const dropdown = document.getElementById(dropdownId);
                const button = document.getElementById(buttonId);
                const isVisible = !dropdown.classList.contains('hidden');

                // Close all dropdowns first
                document.querySelectorAll('[id$="-dropdown"]').forEach(d => {
                    d.classList.add('hidden', 'opacity-0', 'scale-y-95');
                });
                document.querySelectorAll('[id$="-filter-btn"] svg').forEach(svg => {
                    svg.classList.remove('rotate-180');
                });

                if (!isVisible) {
                    // Position dropdown relative to button using viewport coordinates
                    const buttonRect = button.getBoundingClientRect();

                    dropdown.style.left = buttonRect.left + 'px';
                    dropdown.style.top = (buttonRect.bottom + 4) + 'px';

                    dropdown.classList.remove('hidden');
                    // Force reflow to ensure transition works
                    dropdown.offsetHeight;
                    setTimeout(() => {
                        dropdown.classList.remove('opacity-0', 'scale-y-95');
                    }, 10);
                    button.querySelector('svg').classList.add('rotate-180');
                }
            }

            // Initialize filter buttons
            const collegeBtn = document.getElementById('college-filter-btn');
            const yearBtn = document.getElementById('year-filter-btn');
            const activityBtn = document.getElementById('activity-filter-btn');

            if (collegeBtn) {
                collegeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('College button clicked');
                    toggleDropdown('college-dropdown', 'college-filter-btn');
                });
            } else {
                console.error('College filter button not found');
            }

            if (yearBtn) {
                yearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('Year button clicked');
                    toggleDropdown('year-dropdown', 'year-filter-btn');
                });
            } else {
                console.error('Year filter button not found');
            }

            if (activityBtn) {
                activityBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('Activity button clicked');
                    toggleDropdown('activity-dropdown', 'activity-filter-btn');
                });
            } else {
                console.error('Activity filter button not found');
            }

            // Handle filter option clicks
            const filterOptions = document.querySelectorAll('.filter-option');
            console.log('Found', filterOptions.length, 'filter options');

            filterOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('Filter option clicked:', this.dataset.filter, this.dataset.value);

                    const filterType = this.dataset.filter;
                    const value = this.dataset.value;

                    currentFilters[filterType] = value;

                    // Update button text to show current filter
                    const button = document.getElementById(`${filterType}-filter-btn`);
                    const span = button.querySelector('span');
                    span.textContent = value === 'All' ? filterType.charAt(0).toUpperCase() + filterType.slice(1) : value;

                    // Close dropdown
                    const dropdown = document.getElementById(`${filterType}-dropdown`);
                    dropdown.classList.add('hidden', 'opacity-0', 'scale-y-95');
                    button.querySelector('svg').classList.remove('rotate-180');

                    // Apply filters
                    applyFilters();
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('[id$="-filter-btn"]') && !e.target.closest('[id$="-dropdown"]')) {
                    document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
                        dropdown.classList.add('hidden', 'opacity-0', 'scale-y-95');
                    });
                    document.querySelectorAll('[id$="-filter-btn"] svg').forEach(svg => {
                        svg.classList.remove('rotate-180');
                    });
                }
            });

            // Keyboard navigation
            document.querySelectorAll('[id$="-filter-btn"]').forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Initial filter application
            applyFilters();
        })();
    </script>
</body>
</html>