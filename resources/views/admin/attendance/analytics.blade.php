<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Analytics - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
        }
        .period-btn {
            position: relative;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #71717a;
            background: transparent;
            border-radius: 0.5rem;
            transition: all 0.2s;
            border: none;
        }
        .period-btn:hover {
            color: #18181b;
            background: #f4f4f5;
        }
        .period-btn.active {
            color: #18181b;
            background: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .data-type-btn {
            padding: 0.375rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #71717a;
            background: transparent;
            border-radius: 0.375rem;
            transition: all 0.2s;
            border: 1px solid #e4e4e7;
        }
        .data-type-btn:hover {
            background: #fafafa;
            border-color: #d4d4d8;
        }
        .data-type-btn.active {
            color: white;
            background: #18181b;
            border-color: #18181b;
        }
        .chart-container {
            position: relative;
            height: 280px;
            padding-bottom: 0;
        }
        .chart-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.75rem;
            padding: 1.25rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        .rank-item {
            background: white;
            border: 1px solid #f4f4f5;
            border-radius: 0.5rem;
            padding: 0.875rem;
            transition: all 0.2s;
        }
        .rank-item:hover {
            border-color: #e4e4e7;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }
        .rank-bar {
            height: 6px;
            background: #f4f4f5;
            border-radius: 9999px;
            overflow: hidden;
        }
        .rank-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 9999px;
            transition: width 0.6s ease-out;
        }
        .insight-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: #f0fdf4;
            color: #166534;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .chart-wrapper {
            background: white;
            border-radius: 1rem;
            border: 1px solid #f4f4f5;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .section-header {
            border-bottom: 1px solid #f4f4f5;
            padding: 1.5rem;
            background: linear-gradient(to bottom, white 0%, #fafafa 100%);
        }
        .no-data-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: #a1a1aa;
        }
        .loading-spinner {
            border: 3px solid #f4f4f5;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">
        <x-admin-nav-bar />

        <!-- Main Content -->
        <div class="min-h-screen p-8">
            <!-- Header Section -->
            <div class="mb-8 animate-fadeIn">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Attendance Analytics</h1>
                        <p class="text-sm text-gray-500">Track and analyze library attendance patterns</p>
                    </div>
                    <x-attendance-menu />
                </div>

                <!-- Period Selector Card -->
                <div class="chart-wrapper animate-slideUp">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-1">
                                    <button id="dailyBtn" class="period-btn active">Daily</button>
                                    <button id="weeklyBtn" class="period-btn">Weekly</button>
                                    <button id="monthlyBtn" class="period-btn">Monthly</button>
                                    <button id="yearlyBtn" class="period-btn">Yearly</button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span id="timeframeDisplay" class="text-sm font-medium text-gray-700"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Grid -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Activity Distribution -->
                <div class="chart-wrapper animate-slideUp" style="animation-delay: 0.1s;">
                    <div class="section-header">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Activity Distribution</h3>
                                <p class="text-sm text-gray-500 mt-1">Overview of all attendance activities</p>
                            </div>
                            <div id="activityBadge" class="insight-badge">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                </svg>
                                <span id="activityTotal">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="chart-container relative">
                            <canvas id="activityChart"></canvas>
                            <div id="activityNoData" class="hidden no-data-state">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm font-medium">No attendance data available</p>
                                <p class="text-xs text-gray-400 mt-1">Data will appear here once activities are recorded</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <div id="activityRanking" class="space-y-2"></div>
                        <div id="activityInsights" class="mt-4 p-3 bg-blue-50 rounded-lg text-xs text-blue-700"></div>
                    </div>
                </div>

                <!-- College Usage -->
                <div class="chart-wrapper animate-slideUp" style="animation-delay: 0.2s;">
                    <div class="section-header">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">College Usage</h3>
                                <p class="text-sm text-gray-500 mt-1">Usage breakdown by college department</p>
                            </div>
                            <div class="flex items-center gap-6">
                                <div id="collegeBadge" class="insight-badge">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    <span id="collegeTotal">Loading...</span>
                                </div>
                                <div class="flex gap-2">
                                    <button id="studentBtn" class="data-type-btn active">Students</button>
                                    <button id="teacherBtn" class="data-type-btn">Faculty</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="chart-container relative">
                            <canvas id="collegeUsageChart"></canvas>
                            <div id="collegeNoData" class="hidden no-data-state">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-sm font-medium">No college data available</p>
                                <p class="text-xs text-gray-400 mt-1">Select a different time period or data type</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <div id="collegeRanking" class="space-y-2"></div>
                        <div id="collegeInsights" class="mt-4 p-3 bg-purple-50 rounded-lg text-xs text-purple-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activityChart, collegeChart;
            let currentPeriod = 'daily';
            let currentDataType = 'student';

            // Initialize charts
            function initCharts() {
                updateCharts(currentPeriod, currentDataType);
            }

            // Render ranking with modern design
            function renderRanking(containerId, items, total) {
                const el = document.getElementById(containerId);
                if (!el) return;
                if (!items || items.length === 0 || !total) {
                    el.innerHTML = '<div class="no-data-state py-8"><p class="text-sm">No ranking data available</p></div>';
                    return;
                }
                const sorted = [...items].sort((a,b)=>b.value-a.value);
                const top3 = sorted.slice(0, 3);
                const medals = ['🥇', '🥈', '🥉'];
                
                function row(it, idx){
                    const pct = total ? ((it.value/total)*100).toFixed(1) : '0.0';
                    return `
                      <div class="rank-item">
                        <div class="flex items-center justify-between mb-2">
                          <div class="flex items-center gap-2">
                            <span class="text-lg">${medals[idx] || ''}</span>
                            <span class="text-sm font-semibold text-gray-900 truncate max-w-[200px]">${it.label}</span>
                          </div>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">${it.value} visits</span>
                            <span class="text-sm font-bold text-gray-900">${pct}%</span>
                          </div>
                        </div>
                        <div class="rank-bar">
                          <div class="rank-bar-fill" style="width:${pct}%"></div>
                        </div>
                      </div>`;
                }
                el.innerHTML = `
                  <div class="mb-3">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Top Performers</h4>
                  </div>
                  ${top3.map((item, idx) => row(item, idx)).join('')}
                `;
            }

            // Render insights
            function renderInsights(containerId, title, items, total, period){
                const el = document.getElementById(containerId);
                if (!el) return;
                if (!items || items.length === 0 || !total) {
                    el.innerHTML = '';
                    el.classList.add('hidden');
                    return;
                }
                el.classList.remove('hidden');
                const sorted = [...items].sort((a,b)=>b.value-a.value);
                const top = sorted[0];
                const topPct = ((top.value/total)*100).toFixed(1);
                const periodLabel = period.charAt(0).toUpperCase()+period.slice(1);
                el.innerHTML = `
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs leading-relaxed"><strong>${top.label}</strong> leads with <strong>${topPct}%</strong> of total ${title.toLowerCase()} · ${periodLabel} view</p>
                    </div>
                `;
            }

            // Fetch data from API
            async function fetchChartData(period, dataType) {
                try {
                    const response = await fetch(`/admin/attendance/chart-data?period=${period}&type=${dataType}`);
                    if (!response.ok) throw new Error('Failed to fetch data');
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching chart data:', error);
                    return { activities: [], colleges: [] };
                }
            }

            // Update charts
            async function updateCharts(period, dataType = currentDataType) {
                currentPeriod = period;
                currentDataType = dataType;

                showLoadingState();
                const data = await fetchChartData(period, dataType);

                // Activity Chart
                let activityData = (data.activities || []).sort((a, b) => a.cnt - b.cnt);
                const activityLabels = activityData.map(r => r.activity);
                const activityCounts = activityData.map(r => r.cnt);
                const hasActivityData = activityCounts.length > 0 && activityCounts.some(count => count > 0);
                const activityTotal = activityCounts.reduce((a, b) => a + b, 0);

                if (activityChart) activityChart.destroy();

                const activityCtx = document.getElementById('activityChart');
                document.getElementById('activityTotal').textContent = `${activityTotal} Total`;
                
                if (hasActivityData) {
                    const gradientCtx = activityCtx.getContext('2d');
                    const gradient = gradientCtx.createLinearGradient(0, 0, 0, 280);
                    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
                    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

                    activityChart = new Chart(activityCtx, {
                        type: 'line',
                        data: {
                            labels: activityLabels,
                            datasets: [{
                                label: 'Activities',
                                data: activityCounts,
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointBackgroundColor: 'white',
                                pointBorderColor: 'rgb(99, 102, 241)',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointHitRadius: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: { padding: { left: 10, right: 10, top: 20, bottom: 10 } },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 13, weight: '600' },
                                    bodyFont: { size: 12 },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    border: { display: false },
                                    ticks: {
                                        color: '#a1a1aa',
                                        font: { size: 11 },
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f4f4f5', drawBorder: false },
                                    border: { display: false },
                                    ticks: { color: '#a1a1aa', font: { size: 11 }, padding: 8 }
                                }
                            },
                            interaction: { intersect: false, mode: 'index' }
                        }
                    });
                    document.getElementById('activityNoData').classList.add('hidden');
                } else {
                    document.getElementById('activityNoData').classList.remove('hidden');
                }

                renderRanking('activityRanking', activityData.map(x => ({ label: x.activity, value: x.cnt })), activityTotal);
                renderInsights('activityInsights', 'Activities', activityData.map(x => ({ label: x.activity, value: x.cnt })), activityTotal, currentPeriod);

                // College Chart
                let collegeData = (data.colleges || []).sort((a, b) => a.cnt - b.cnt);
                const collegeLabels = collegeData.map(r => r.college);
                const collegeCounts = collegeData.map(r => r.cnt);
                const hasCollegeData = collegeCounts.length > 0 && collegeCounts.some(count => count > 0);
                const collegeTotal = collegeCounts.reduce((a, b) => a + b, 0);

                if (collegeChart) collegeChart.destroy();

                const collegeCtx = document.getElementById('collegeUsageChart');
                document.getElementById('collegeTotal').textContent = `${collegeTotal} Total`;
                
                if (hasCollegeData) {
                    const gradientCtx = collegeCtx.getContext('2d');
                    const gradient = gradientCtx.createLinearGradient(0, 0, 0, 280);
                    gradient.addColorStop(0, 'rgba(168, 85, 247, 0.2)');
                    gradient.addColorStop(1, 'rgba(168, 85, 247, 0.01)');

                    collegeChart = new Chart(collegeCtx, {
                        type: 'line',
                        data: {
                            labels: collegeLabels,
                            datasets: [{
                                label: 'Colleges',
                                data: collegeCounts,
                                borderColor: 'rgb(168, 85, 247)',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointBackgroundColor: 'white',
                                pointBorderColor: 'rgb(168, 85, 247)',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointHitRadius: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: { padding: { left: 10, right: 10, top: 20, bottom: 10 } },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 13, weight: '600' },
                                    bodyFont: { size: 12 },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    border: { display: false },
                                    ticks: {
                                        color: '#a1a1aa',
                                        font: { size: 11 },
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f4f4f5', drawBorder: false },
                                    border: { display: false },
                                    ticks: { color: '#a1a1aa', font: { size: 11 }, padding: 8 }
                                }
                            },
                            interaction: { intersect: false, mode: 'index' }
                        }
                    });
                    document.getElementById('collegeNoData').classList.add('hidden');
                } else {
                    document.getElementById('collegeNoData').classList.remove('hidden');
                }

                renderRanking('collegeRanking', collegeData.map(x => ({ label: x.college, value: x.cnt })), collegeTotal);
                renderInsights('collegeInsights', currentDataType === 'student' ? 'Student Visits' : 'Faculty Visits', collegeData.map(x => ({ label: x.college, value: x.cnt })), collegeTotal, currentPeriod);

                updatePeriodButtonStyles(period);
                updateDataTypeButtonStyles(dataType);
                hideLoadingState();
            }

            function showLoadingState() {
                // Add loading indicators if needed
            }

            function hideLoadingState() {
                // Remove loading indicators if needed
            }

            function updatePeriodButtonStyles(activePeriod) {
                ['dailyBtn', 'weeklyBtn', 'monthlyBtn', 'yearlyBtn'].forEach(btnId => {
                    const btn = document.getElementById(btnId);
                    btn.classList.toggle('active', btnId.replace('Btn', '') === activePeriod);
                });
                updateTimeframeDisplay(activePeriod);
            }

            function updateTimeframeDisplay(period) {
                const now = new Date();
                const options = { timeZone: 'Asia/Manila', day: '2-digit', month: 'short', year: 'numeric' };
                let displayText = '';

                switch (period) {
                    case 'daily':
                        displayText = now.toLocaleDateString('en-GB', options);
                        break;
                    case 'weekly':
                        const weekEnd = new Date(now);
                        weekEnd.setDate(now.getDate() + (6 - now.getDay()));
                        displayText = `Week ending ${weekEnd.toLocaleDateString('en-GB', options)}`;
                        break;
                    case 'monthly':
                        displayText = now.toLocaleDateString('en-GB', { timeZone: 'Asia/Manila', month: 'long', year: 'numeric' });
                        break;
                    case 'yearly':
                        displayText = now.getFullYear().toString();
                        break;
                }

                document.getElementById('timeframeDisplay').textContent = displayText;
            }

            function updateDataTypeButtonStyles(activeType) {
                ['studentBtn', 'teacherBtn'].forEach(btnId => {
                    const btn = document.getElementById(btnId);
                    btn.classList.toggle('active', btnId.replace('Btn', '') === activeType);
                });
            }

            // Event listeners
            document.getElementById('dailyBtn').addEventListener('click', () => updateCharts('daily'));
            document.getElementById('weeklyBtn').addEventListener('click', () => updateCharts('weekly'));
            document.getElementById('monthlyBtn').addEventListener('click', () => updateCharts('monthly'));
            document.getElementById('yearlyBtn').addEventListener('click', () => updateCharts('yearly'));
            document.getElementById('studentBtn').addEventListener('click', () => updateCharts(currentPeriod, 'student'));
            document.getElementById('teacherBtn').addEventListener('click', () => updateCharts(currentPeriod, 'teacher'));

            // Initialize
            initCharts();
        });
    </script>
</body>
</html>