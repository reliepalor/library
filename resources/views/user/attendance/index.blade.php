<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library  </title>
      <link rel="icon" type="image/x-icon" href="/images/library.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/heroicons@2.0.16/dist/20/outline.min.js"></script>

    <!-- Styles and Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Add this in the head section after the existing styles -->
    <style>
        .college-CICS { 
            background-color: #c77dff;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CTED { 
            background-color: #90e0ef;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CCJE { 
            background-color: #ff4d6d;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CHM { 
            background-color: #ffc8dd;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CBEA { 
            background-color: #fae588;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .college-CA { 
            background-color: #80ed99;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>
    <x-header />

    <div class="flex justify-center px-6 border border-gray-200 shadow-sm py-8 bg-white rounded-lg max-w-7xl mx-auto space-y-6 mt-20">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Today's Attendance</h2>
            </div>

            <!-- Attendance Logs -->
            <div class="mt-10 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between items-center p-6 bg-gray-100 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Today's Attendance</h3>
                </div>
                <div class="overflow-x-auto p-4">
                    <table class="w-full table-auto text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b">
                            <tr>
                                <th class="px-6 py-3">Student ID</th>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">College</th>
                                <th class="px-6 py-3">Year</th>
                                <th class="px-6 py-3">Activity</th>
                                <th class="px-6 py-3">Login</th>
                                <th class="px-6 py-3">Logout</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="attendance-table-body">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $attendance->student_id }}</td>
                                    <td class="px-6 py-4">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="college-{{ $attendance->student->college ?? '' }}">{{ $attendance->student->college ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">{{ $attendance->student->year ?? '' }}</td>
                                    <td class="px-6 py-4">
                                        @if(str_contains($attendance->activity, 'Borrow'))
                                            @php
                                                $parts = explode(':', $attendance->activity);
                                                $activity = $parts[0];
                                                $bookCode = $parts[1] ?? 'N/A';
                                            @endphp
                                            {{ $activity }}: {{ $bookCode }}
                                        @else
                                            {{ $attendance->activity ?? '' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                                    <td class="px-6 py-4">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No attendance logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-20"></div>
        <x-footer />
</body>
</html>