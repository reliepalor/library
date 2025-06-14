@php
    $dailyTrends = $dailyTrends ?? collect();
    $collegeDistribution = $collegeDistribution ?? collect();
    $activityDistribution = $activityDistribution ?? collect();
    $summary = $summary ?? [
        'total_attendance' => 0,
        'average_daily' => 0,
        'most_active_college' => 'N/A',
        'most_common_activity' => 'N/A'
    ];
    $dateFrom = $dateFrom ?? request('date_from', now()->subDays(7)->format('Y-m-d'));
    $dateTo = $dateTo ?? request('date_to', now()->format('Y-m-d'));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Insights - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <!-- Sidebar -->
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
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Dashboard</span>
                </x-nav-link>

                <!-- Students Link -->
                <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.index')" class="flex items-center px-4 py-3">
                    <img src="/images/study.png" alt="" width="25" height="25">
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Students</span>
                </x-nav-link>

                <!-- Attendance Link -->
                <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Attendance</span>
                </x-nav-link>

                <!-- Books Link -->
                <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.index')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Books</span>
                </x-nav-link>

                <!-- Borrow Requests Link -->
                <x-nav-link :href="route('admin.borrow.requests')" :active="request()->routeIs('admin.borrow.requests')" class="flex items-center px-5 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
                        <path d="M4 4h12v2H4V4zm0 4h12v2H4V8zm0 4h8v2H4v-2z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="ml-3 transition-opacity duration-300 text-gray-800">Borrow Requests</span>
                </x-nav-link>
            </div>

            <!-- User Profile Section -->
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
                                <div x-show="sidebarExpanded" class="ml-3 overflow-hidden">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="fixed flex flex-row items-center justify-center rounded-lg bg-gray-400 shadow-lg z-50 overflow-hidden transition-all duration-300 ease-out w-[18.25rem] h-[10vh] absolute top-[-65px]"
                                :style="sidebarExpanded ? 'left: 15.5em' : 'left: 7rem'">
                                <div class="flex flex-row items-center space-x-4 px-4 py-2 bg-white dark:bg-white rounded-lg shadow-full border border-gray-200">
                                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center space-x-2 text-sm text-gray-700">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0H4.5z" />
                                        </svg>
                                        <span class="text-gray-800">{{ __('Profile') }}</span>
                                    </x-dropdown-link>
                                    <div class="h-6 w-px bg-gray-300"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center space-x-2 text-sm text-gray-700">
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="gray" stroke-width="1.5" viewBox="0 0 24 24">
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
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Attendance Insights</h1>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('admin.attendance.index') }}" class="shadcn-button">
                        Attendance
                    </a>
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

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Attendance -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Attendance</p>
                            <p class="text-2xl font-semibold text-gray-900" id="totalAttendance">0</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Duration -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg. Duration</p>
                            <p class="text-2xl font-semibold text-gray-900" id="avgDuration">0h</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Most Active College -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Most Active College</p>
                            <p class="text-2xl font-semibold text-gray-900" id="mostActiveCollege">-</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Peak Day -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Peak Day</p>
                            <p class="text-2xl font-semibold text-gray-900" id="peakDay">-</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Weekly Pattern -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Attendance Pattern</h3>
                    <div class="h-80">
                        <canvas id="weeklyPatternChart"></canvas>
                    </div>
                </div>

                <!-- Duration Distribution -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Visit Duration Distribution</h3>
                    <div class="h-80">
                        <canvas id="durationDistributionChart"></canvas>
                    </div>
                </div>

                <!-- College Activity Comparison -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">College Activity Comparison</h3>
                    <div class="h-80">
                        <canvas id="collegeActivityChart"></canvas>
                    </div>
                </div>

                <!-- Activity Trends -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Trends</h3>
                    <div class="h-80">
                        <canvas id="activityTrendsChart"></canvas>
                    </div>
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
        .shadcn-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: white;
        }
        .shadcn-input:focus {
            outline: none;
            border-color: #18181b;
            box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch insights data
            fetch('/admin/attendance/insights-data')
                .then(response => response.json())
                .then(data => {
                    // Update key metrics
                    document.getElementById('totalAttendance').textContent = data.total_attendance;
                    document.getElementById('avgDuration').textContent = data.avg_duration + 'h';
                    document.getElementById('mostActiveCollege').textContent = data.most_active_college;
                    document.getElementById('peakDay').textContent = data.peak_day;

                    // Weekly Pattern Chart
                    new Chart(document.getElementById('weeklyPatternChart'), {
                        type: 'bar',
                        data: {
                            labels: data.weekly_pattern.labels,
                            datasets: [{
                                label: 'Attendance Count',
                                data: data.weekly_pattern.data,
                                backgroundColor: '#3b82f6',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });

                    // Duration Distribution Chart
                    new Chart(document.getElementById('durationDistributionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: data.duration_distribution.labels,
                            datasets: [{
                                data: data.duration_distribution.data,
                                backgroundColor: [
                                    '#f3e8ff', // < 1 hour
                                    '#dbeafe', // 1-2 hours
                                    '#fee2e2', // 2-3 hours
                                    '#fce7f3', // 3-4 hours
                                    '#fef9c3', // > 4 hours
                                ],
                                borderColor: [
                                    '#6b21a8',
                                    '#1e40af',
                                    '#991b1b',
                                    '#9d174d',
                                    '#854d0e',
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });

                    // College Activity Comparison Chart
                    new Chart(document.getElementById('collegeActivityChart'), {
                        type: 'radar',
                        data: {
                            labels: data.college_activity.labels,
                            datasets: [{
                                label: 'Activity Score',
                                data: data.college_activity.data,
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderColor: '#3b82f6',
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#3b82f6'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });

                    // Activity Trends Chart
                    new Chart(document.getElementById('activityTrendsChart'), {
                        type: 'line',
                        data: {
                            labels: data.activity_trends.labels,
                            datasets: data.activity_trends.datasets.map(dataset => ({
                                label: dataset.label,
                                data: dataset.data,
                                borderColor: dataset.color,
                                tension: 0.1,
                                fill: false
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                });
        });
    </script>
</body>
</html> 