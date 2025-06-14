<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <nav class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-100 transition-all duration-300 ease-in-out"
            :class="{'w-16': !sidebarExpanded, 'w-60': sidebarExpanded}">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 border-b dark:border-gray-300">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
                    <img src="/images/library.png" alt="" width="30" height="30">           
                    <span x-show="sidebarExpanded" class="ml-2 text-gray-800 font-semibold transition-opacity duration-300" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Library
                    </span>
                </a>
            </div>

            <!-- Main Navigation -->
            <div class="mt-4 grid">
                <!-- Dashboard Link -->
                <x-nav-link :href="route('admin.auth.dashboard')" :active="request()->routeIs('admin.auth.dashboard')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Dashboard
                    </span>
                </x-nav-link>
                <!-- Students Link -->
                <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.index')" class="flex items-center px-4 py-3">
                    <img src="/images/study.png" alt="" width="25" height="25">
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Students
                    </span>
                </x-nav-link>
                <!-- Attendance Link -->
                <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Attendance
                    </span>
                </x-nav-link>
                <!-- Books Link -->
                <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.index')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800" 
                          x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Books
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('admin.borrow.requests')" :active="request()->routeIs('admin.borrow.requests')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray"><path d="M4 4h12v2H4V4zm0 4h12v2H4V8zm0 4h8v2H4v-2z"/></svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Borrow Requests</span>
                </x-nav-link>
            </div>

            <!-- User Profile Section (Bottom) -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200 p-4 bg-white dark:bg-gray-100">
                <div class="relative">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button class="flex items-center w-full text-left focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-200 rounded-md transition-colors duration-200 p-1">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 rounded-full bg-gray-200 p-1 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div x-show="sidebarExpanded" class="ml-3 overflow-hidden transition-all duration-300 ease-in-out" 
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                </div>
                                <div x-show="sidebarExpanded" class="ml-auto transition-transform duration-200 ease-in-out" 
                                    :class="{ 'rotate-180': open }">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="fixed flex flex-row items-center justify-center rounded-lg bg-gray-400 shadow-lg z-50 overflow-hidden transition-all duration-300 ease-out w-[18.25rem] h-[10vh] absolute top-[-65px]"
                                :style="sidebarExpanded ? 'left: 15.5em' : 'left: 7rem'"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95">
                                <div class="flex flex-row items-center space-x-4 px-4 py-2 bg-white dark:bg-white rounded-lg shadow-full border border-gray-200">
                                    <!-- Profile Link -->
                                    <x-dropdown-link :href="route('profile.edit')"
                                                     class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-300 px-3 py-2 rounded transition-colors duration-200">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0H4.5z" />
                                        </svg>
                                        <span class="text-gray-800">{{ __('Profile') }}</span>
                                    </x-dropdown-link>
                                    <!-- Vertical Divider -->
                                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                                    <!-- Logout Form + Link -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                         onclick="event.preventDefault(); this.closest('form').submit();"
                                                         class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-300 px-3 py-2 rounded transition-colors duration-200">
                                            <svg class="w-8 h-8 text-gray-500 dark:text-gray-300" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3h-8.25m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                            <span class="text-gray-800">{{ __('Log Out') }}</span>
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Toggle Button -->
            <button @click="sidebarExpanded = !sidebarExpanded" 
                    class="absolute -right-3 top-6 bg-white dark:bg-white border dark:border-gray-300 rounded-full p-1 shadow-md hover:bg-gray-100 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                    <path x-show="!sidebarExpanded" fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    <path x-show="sidebarExpanded" fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </nav>

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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['student_name'] }}</td>
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
</body>
</html> 