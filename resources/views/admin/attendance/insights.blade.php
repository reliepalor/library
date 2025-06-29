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
        <x-admin-nav-bar />


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