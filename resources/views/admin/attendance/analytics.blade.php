<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Analytics - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <x-admin-nav-bar />


        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Attendance Analytics</h1>
                </div>
                <x-attendance-menu />
            </div>

            <!-- Date Range Filter -->
            <div class="shadcn-card p-6 mb-8">
                <form action="{{ route('admin.attendance.analytics') }}" method="GET" class="flex flex-wrap gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->subDays(7)->format('Y-m-d')) }}"
                            class="shadcn-input">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}"
                            class="shadcn-input">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="shadcn-button">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Analytics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Daily Attendance Trends -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Daily Attendance Trends</h3>
                    <div class="h-80">
                        <canvas id="attendanceTrendsChart"></canvas>
                    </div>
                </div>

                <!-- College-wise Distribution -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">College-wise Distribution</h3>
                    <div class="h-80">
                        <canvas id="collegeDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Activity Distribution -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Distribution</h3>
                    <div class="h-80">
                        <canvas id="activityDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Peak Hours Analysis -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Peak Hours Analysis</h3>
                    <div class="h-80">
                        <canvas id="peakHoursChart"></canvas>
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
            // Fetch chart data
            fetch(`{{ route('admin.attendance.chart-data') }}?date_from=${document.getElementById('date_from').value}&date_to=${document.getElementById('date_to').value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error fetching chart data:', data.message);
                        return;
                    }

                    // Daily Attendance Trends Chart
                    new Chart(document.getElementById('attendanceTrendsChart'), {
                        type: 'line',
                        data: {
                            labels: data.dates,
                            datasets: [{
                                label: 'Attendance Count',
                                data: data.attendance_counts,
                                borderColor: '#3b82f6',
                                tension: 0.1,
                                fill: true,
                                backgroundColor: 'rgba(59, 130, 246, 0.1)'
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

                    // College Distribution Chart
                    new Chart(document.getElementById('collegeDistributionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: data.colleges,
                            datasets: [{
                                data: data.college_counts,
                                backgroundColor: [
                                    '#f3e8ff', // CICS
                                    '#dbeafe', // CTED
                                    '#fee2e2', // CCJE
                                    '#fce7f3', // CHM
                                    '#fef9c3', // CBEA
                                    '#dcfce7'  // CA
                                ],
                                borderColor: [
                                    '#6b21a8', // CICS
                                    '#1e40af', // CTED
                                    '#991b1b', // CCJE
                                    '#9d174d', // CHM
                                    '#854d0e', // CBEA
                                    '#166534'  // CA
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

                    // Activity Distribution Chart
                    new Chart(document.getElementById('activityDistributionChart'), {
                        type: 'pie',
                        data: {
                            labels: data.activities,
                            datasets: [{
                                data: data.activity_counts,
                                backgroundColor: [
                                    '#10b981', // Present
                                    '#ef4444', // Absent
                                    '#8b5cf6'  // Borrowed
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

                    // Peak Hours Analysis Chart
                    new Chart(document.getElementById('peakHoursChart'), {
                        type: 'bar',
                        data: {
                            labels: data.hours,
                            datasets: [{
                                label: 'Attendance Count',
                                data: data.hourly_counts,
                                backgroundColor: '#8b5cf6',
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
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        });
    </script>
</body>
</html> 