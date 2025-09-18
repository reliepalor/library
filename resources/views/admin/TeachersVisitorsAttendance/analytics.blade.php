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
                    <!-- Top Activity and College Lists -->
                <div class="shadcn-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Highlights</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Top Activities</h4>
                            <ul class="space-y-2">
                                @foreach(($stats['activities'] ?? []) as $row)
                                    <li class="flex items-center justify-between">
                                        <span class="truncate pr-3">{{ $row->activity }}</span>
                                        <span class="text-sm px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $row->cnt }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Most Active Colleges</h4>
                            <ul class="space-y-2">
                                @foreach(($stats['colleges'] ?? []) as $row)
                                    <li class="flex items-center justify-between">
                                        <span class="truncate pr-3">{{ $row->college }}</span>
                                        <span class="text-sm px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $row->cnt }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
                <x-attendance-menu />
            </div>

            <!-- Range Filter -->
            <div class="shadcn-card p-6 mb-8">
                <form action="{{ route('admin.attendance.analytics') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="days" class="block text-sm font-medium text-gray-700">Range</label>
                        <select id="days" name="days" class="shadcn-input">
                            @foreach([7,14,30,60,90] as $d)
                                <option value="{{ $d }}" {{ (int)request('days', 30) === $d ? 'selected' : '' }}>{{ $d }} days</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="shadcn-button">Apply</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="shadcn-card p-5">
                    <p class="text-sm text-gray-500">Total Visits</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['totals']['visits'] ?? 0) }}</p>
                </div>
                <div class="shadcn-card p-5">
                    <p class="text-sm text-gray-500">Unique Students</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['totals']['unique_students'] ?? 0) }}</p>
                </div>
                <div class="shadcn-card p-5">
                    <p class="text-sm text-gray-500">Today</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['totals']['today'] ?? 0) }}</p>
                </div>
                <div class="shadcn-card p-5">
                    <p class="text-sm text-gray-500">Avg Duration</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ ($stats['totals']['avg_duration_min'] ?? 0) }} mins</p>
                </div>
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
            const stats = @json($stats);
            // Daily Attendance Trends Chart
            new Chart(document.getElementById('attendanceTrendsChart'), {
                type: 'line',
                data: {
                    labels: stats.trend?.labels || [],
                    datasets: [{
                        label: 'Attendance Count',
                        data: stats.trend?.data || [],
                        borderColor: '#3b82f6',
                        tension: 0.2,
                        fill: true,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // College Distribution Chart
            const collegeLabels = (stats.colleges || []).map(r => r.college);
            const collegeCounts = (stats.colleges || []).map(r => r.cnt);
            new Chart(document.getElementById('collegeDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: collegeLabels,
                    datasets: [{
                        data: collegeCounts,
                        backgroundColor: ['#f3e8ff','#dbeafe','#fee2e2','#fce7f3','#fef9c3','#dcfce7','#e5e7eb'],
                        borderColor: ['#6b21a8','#1e40af','#991b1b','#9d174d','#854d0e','#166534','#374151'],
                        borderWidth: 2
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Activity Distribution Chart
            const activityLabels = (stats.activities || []).map(r => r.activity);
            const activityCounts = (stats.activities || []).map(r => r.cnt);
            new Chart(document.getElementById('activityDistributionChart'), {
                type: 'pie',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        data: activityCounts,
                        backgroundColor: ['#10b981','#ef4444','#8b5cf6','#f59e0b','#3b82f6','#22c55e','#eab308'],
                        borderWidth: 2
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Peak Hours Analysis (derived from trend by hour if available later)
            // Placeholder: we use the same trend to display distribution over labels
            new Chart(document.getElementById('peakHoursChart'), {
                type: 'bar',
                data: {
                    labels: stats.trend?.labels || [],
                    datasets: [{ label: 'Daily Count', data: stats.trend?.data || [], backgroundColor: '#8b5cf6', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
</body>
</html> 