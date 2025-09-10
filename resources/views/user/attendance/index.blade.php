<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        <!-- Hero Section -->
        <div class="header-section p-8 text-center text-white fade-in">
            <x-attendance-hero-section/>
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
                                            <img src="{{ $attendance->student && $attendance->student->user && $attendance->student->user->profile_picture ? asset('storage/' . $attendance->student->user->profile_picture) : asset('images/default-profile.png') }}" 
                                                alt="Profile Picture" 
                                                class="profile-img" />
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

        // Realtime updates for user attendance table
        (function() {
            // Asset bases for images
            const storageBase = "{{ asset('storage') }}/";
            const defaultProfile = "{{ asset('images/default-profile.png') }}";
            const tbody = document.getElementById('attendance-table-body');
            if (!tbody) return;

            const getActivityBadge = (text) => {
                if (!text) return '';
                const lower = String(text).toLowerCase();
                if (lower.includes('wait for approval')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">${text}</span>`;
                }
                if (lower.startsWith('borrow:')) {
                    const parts = String(text).split(':');
                    const code = (parts[1] || '').trim();
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Borrow: ${code}</span>`;
                }
                if (lower.includes('borrow book rejected')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">${text}</span>`;
                }
                if (lower.includes('book returned')) {
                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">${text}</span>`;
                }
                return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">${text}</span>`;
            };

            let inFlight = null;
            const fetchRealtime = async () => {
                try {
                    // Prevent overlapping requests
                    if (inFlight) {
                        return;
                    }
                    inFlight = true;
                    const res = await fetch('{{ route('user.attendance.realtime') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json?.success || !json?.data) return;

                    const rows = json.data.todayAttendance || [];
                    if (!rows.length) {
                        tbody.innerHTML = `<tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-lg font-medium">No attendance logs yet</p>
                                    <p class="text-sm">Student activities will appear here as they occur</p>
                                </div>
                            </td>
                        </tr>`;
                        return;
                    }

                    const frag = document.createDocumentFragment();
                    rows.forEach(a => {
                        const imgUrl = a.profile_picture ? (storageBase + a.profile_picture) : defaultProfile;
                        const tr = document.createElement('tr');
                        tr.className = 'table-row';
                        tr.innerHTML = `
                            <td class="px-6 py-4 font-semibold text-gray-800 text-sm">${a.student_id}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img src="${imgUrl}" alt="Profile Picture" class="profile-img" onerror="this.onerror=null;this.src='${defaultProfile}'" />
                                    <div>
                                        <div class="font-medium text-gray-900">${a.student_name || ''}</div>
                                        <div class="text-sm text-gray-500 sm:hidden">${a.student_id}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="college-${a.college || ''}">${a.college || ''}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600 hidden sm:table-cell">${a.year || ''}</td>
                            <td class="px-6 py-4">${getActivityBadge(a.activity)}</td>
                            <td class="px-6 py-4"><div class="text-sm font-medium text-gray-900">${a.time_in || '-'}</div><div class="text-xs text-gray-500 sm:hidden">Year: ${a.year || ''}</div></td>
                            <td class="px-6 py-4 hidden sm:table-cell"><div class="text-sm font-medium text-gray-900">${a.time_out || '-'}</div><div class="text-xs ${a.has_logout ? 'text-emerald-600' : 'text-blue-600'}">${a.has_logout ? 'Completed' : 'Active'}</div></td>
                        `;
                        frag.appendChild(tr);
                    });
                    tbody.innerHTML = '';
                    tbody.appendChild(frag);
                } catch (e) {
                    console.debug('User realtime fetch failed:', e);
                } finally {
                    inFlight = false;
                }
            };

            // Initial load and poll every 5s
            fetchRealtime();
            setInterval(fetchRealtime, 3000);
        })();
    </script>
</body>
</html>