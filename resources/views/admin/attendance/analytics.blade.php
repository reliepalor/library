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
        body {
            font-family: 'Inter', sans-serif;
        }
        .period-btn {
            @apply inline-flex h-9 items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-muted text-muted-foreground hover:bg-accent hover:text-accent-foreground px-4;
        }
        .period-btn.active {
            @apply bg-primary text-primary-foreground border-primary;
        }
        .data-type-btn {
            @apply inline-flex h-8 items-center justify-center whitespace-nowrap rounded-md text-xs font-medium border transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-muted text-muted-foreground hover:bg-accent hover:text-accent-foreground px-3;
        }
        .data-type-btn.active {
            @apply bg-primary text-primary-foreground border-primary;
        }
        .no-data {
            @apply flex items-center justify-center h-full bg-muted text-muted-foreground text-sm rounded-lg;
        }
        .chart-container {
            position: relative;
            height: 0;
            padding-bottom: 75%; /* 4:3 aspect ratio for responsiveness */
        }
        .chart-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="bg-background" x-data="{ sidebarExpanded: true }">
    <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
        <x-admin-nav-bar />

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Attendance Analytics</h1>
                </div>
                <x-attendance-menu />
            </div>

            <!-- Time Period Selector -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-medium text-muted-foreground">Time Period:</label>
                        <div class="flex gap-2">
                            <button id="dailyBtn" class="period-btn active">Daily</button>
                            <button id="weeklyBtn" class="period-btn">Weekly</button>
                            <button id="monthlyBtn" class="period-btn">Monthly</button>
                            <button id="yearlyBtn" class="period-btn">Yearly</button>
                        </div>
                    </div>
                    <div id="timeframeDisplay" class="text-sm text-muted-foreground">
                        <!-- Timeframe will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Analytics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Activity Distribution -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="p-6">
                        <div class="space-y-1">
                            <h3 class="text-lg font-semibold text-foreground">Activity Distribution</h3>
                            <p class="text-sm text-muted-foreground">Showing distribution of attendance activities</p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="chart-container relative">
                            <canvas id="activityChart"></canvas>
                            <div id="activityNoData" class="hidden absolute inset-0 flex items-center justify-center text-muted-foreground text-sm bg-muted/50 rounded-lg">
                                No attendance data available for this period
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-0 flex flex-col gap-2 text-xs">
                        <div id="activityTotal" class="text-muted-foreground"></div>
                    </div>
                </div>

                <!-- College Usage -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <h3 class="text-lg font-semibold text-foreground">College Usage</h3>
                                <p class="text-sm text-muted-foreground">Showing usage by college</p>
                            </div>
                            <div class="flex gap-2">
                                <button id="studentBtn" class="data-type-btn active">Student</button>
                                <button id="teacherBtn" class="data-type-btn">Teacher/Visitor</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="chart-container relative">
                            <canvas id="collegeUsageChart"></canvas>
                            <div id="collegeNoData" class="hidden absolute inset-0 flex items-center justify-center text-muted-foreground text-sm bg-muted/50 rounded-lg">
                                No data available
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-0 flex flex-col gap-2 text-xs">
                        <div id="collegeTotal" class="text-muted-foreground"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stats = @json($stats);
            let activityChart, collegeChart;
            let currentPeriod = 'daily';
            let currentDataType = 'student';

            // Initialize charts
            function initCharts() {
                updateCharts(currentPeriod, currentDataType);
            }

            // Fetch data from API based on period and data type
            async function fetchChartData(period, dataType) {
                try {
                    const response = await fetch(`/admin/attendance/chart-data?period=${period}&type=${dataType}`);
                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching chart data:', error);
                    // Return empty data structure as fallback
                    return {
                        activities: [],
                        colleges: []
                    };
                }
            }

            // Update charts based on period and data type
            async function updateCharts(period, dataType = currentDataType) {
                currentPeriod = period;
                currentDataType = dataType;

                // Show loading state
                showLoadingState();

                // Fetch data from API
                const data = await fetchChartData(period, dataType);

                // Update Activity Distribution Chart (Area Chart with Gradient)
                let activityData = (data.activities || []).sort((a, b) => a.cnt - b.cnt);
                const activityLabels = activityData.map(r => r.activity);
                const activityCounts = activityData.map(r => r.cnt);
                const hasActivityData = activityCounts.length > 0 && activityCounts.some(count => count > 0);
                const activityTotal = activityCounts.reduce((a, b) => a + b, 0);

                if (activityChart) {
                    activityChart.destroy();
                }

                const activityCtx = document.getElementById('activityChart');
                document.getElementById('activityTotal').textContent = `Total: ${activityTotal}`;
                if (hasActivityData) {
                    const gradientCtx = activityCtx.getContext('2d');
                    const gradient = gradientCtx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

                    activityChart = new Chart(activityCtx, {
                        type: 'line',
                        data: {
                            labels: activityLabels,
                            datasets: [{
                                label: 'Activities',
                                data: activityCounts,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointBackgroundColor: 'rgb(59, 130, 246)',
                                pointBorderColor: 'rgb(59, 130, 246)',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHitRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 12,
                                    right: 12
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'hsl(var(--muted-foreground))',
                                        maxRotation: 45,
                                        callback: function(value) {
                                            return value.toString().length > 10 ? value.toString().slice(0, 10) + '...' : value;
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'hsl(var(--muted-foreground))'
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            animation: {
                                duration: 1000
                            }
                        }
                    });
                    document.getElementById('activityNoData').classList.add('hidden');
                } else {
                    document.getElementById('activityNoData').classList.remove('hidden');
                    document.getElementById('activityTotal').textContent = 'Total: 0';
                }

                // Update College Usage Chart (Area Chart with Gradient)
                let collegeData = (data.colleges || []).sort((a, b) => a.cnt - b.cnt);
                const collegeLabels = collegeData.map(r => r.college);
                const collegeCounts = collegeData.map(r => r.cnt);
                const hasCollegeData = collegeCounts.length > 0 && collegeCounts.some(count => count > 0);
                const collegeTotal = collegeCounts.reduce((a, b) => a + b, 0);

                if (collegeChart) {
                    collegeChart.destroy();
                }

                const collegeCtx = document.getElementById('collegeUsageChart');
                document.getElementById('collegeTotal').textContent = `Total: ${collegeTotal}`;
                if (hasCollegeData) {
                    const gradientCtx = collegeCtx.getContext('2d');
                    const gradient = gradientCtx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

                    collegeChart = new Chart(collegeCtx, {
                        type: 'line',
                        data: {
                            labels: collegeLabels,
                            datasets: [{
                                label: 'Colleges',
                                data: collegeCounts,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointBackgroundColor: 'rgb(59, 130, 246)',
                                pointBorderColor: 'rgb(59, 130, 246)',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHitRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 12,
                                    right: 12
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'hsl(var(--muted-foreground))',
                                        maxRotation: 45,
                                        callback: function(value) {
                                            return value.toString().length > 10 ? value.toString().slice(0, 10) + '...' : value;
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'hsl(var(--muted-foreground))'
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            animation: {
                                duration: 1000
                            }
                        }
                    });
                    document.getElementById('collegeNoData').classList.add('hidden');
                } else {
                    document.getElementById('collegeNoData').classList.remove('hidden');
                    document.getElementById('collegeTotal').textContent = 'Total: 0';
                }

                // Update button styles
                updatePeriodButtonStyles(period);
                updateDataTypeButtonStyles(dataType);

                // Hide loading state
                hideLoadingState();
            }

            // Show loading state
            function showLoadingState() {
                // You can add loading indicators here if needed
            }

            // Hide loading state
            function hideLoadingState() {
                // You can remove loading indicators here if needed
            }

            // Update period button active states and timeframe display
            function updatePeriodButtonStyles(activePeriod) {
                const buttons = ['dailyBtn', 'weeklyBtn', 'monthlyBtn', 'yearlyBtn'];
                buttons.forEach(btnId => {
                    const btn = document.getElementById(btnId);
                    if (btnId.replace('Btn', '') === activePeriod) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // Update timeframe display
                updateTimeframeDisplay(activePeriod);
            }

            // Update timeframe display based on selected period
            function updateTimeframeDisplay(period) {
                const now = new Date();
                const tz = 'Asia/Manila';
                const options = { timeZone: tz };
                let displayText = '';

                switch (period) {
                    case 'daily':
                        displayText = now.toLocaleDateString('en-GB', { ...options, day: '2-digit', month: '2-digit', year: 'numeric' });
                        break;
                    case 'weekly':
                        const weekEnd = new Date(now);
                        weekEnd.setDate(now.getDate() + (6 - now.getDay()));
                        displayText = weekEnd.toLocaleDateString('en-GB', { ...options, day: '2-digit', month: '2-digit', year: 'numeric' });
                        break;
                    case 'monthly':
                        const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        displayText = monthEnd.toLocaleDateString('en-GB', { ...options, day: '2-digit', month: '2-digit', year: 'numeric' });
                        break;
                    case 'yearly':
                        const yearEnd = new Date(now.getFullYear(), 11, 31);
                        displayText = yearEnd.toLocaleDateString('en-GB', { ...options, day: '2-digit', month: '2-digit', year: 'numeric' });
                        break;
                }

                document.getElementById('timeframeDisplay').textContent = displayText;
            }

            // Update data type button active states
            function updateDataTypeButtonStyles(activeType) {
                const buttons = ['studentBtn', 'teacherBtn'];
                buttons.forEach(btnId => {
                    const btn = document.getElementById(btnId);
                    if (btnId.replace('Btn', '') === activeType) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }

            // Event listeners for period buttons
            document.getElementById('dailyBtn').addEventListener('click', () => updateCharts('daily'));
            document.getElementById('weeklyBtn').addEventListener('click', () => updateCharts('weekly'));
            document.getElementById('monthlyBtn').addEventListener('click', () => updateCharts('monthly'));
            document.getElementById('yearlyBtn').addEventListener('click', () => updateCharts('yearly'));

            // Event listeners for data type buttons
            document.getElementById('studentBtn').addEventListener('click', () => updateCharts(currentPeriod, 'student'));
            document.getElementById('teacherBtn').addEventListener('click', () => updateCharts(currentPeriod, 'teacher'));

            // Initialize
            initCharts();
        });
    </script>
</body>
</html>