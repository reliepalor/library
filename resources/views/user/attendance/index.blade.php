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

    <!-- Styles and Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Add this in the head section after the existing styles -->
    <style>
        .college-CICS { 
            background-color: #b655ff;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            color: #1f0036;
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 space-y-8 transition-all duration-300 hover:shadow-xl">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">Today's Attendance</h2>
            </div>

            <!-- Attendance Logs -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300">
                <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Attendance Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-sm text-left text-gray-700">
                        <thead class="bg-blue-50 text-gray-600 uppercase text-xs font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 py-4">Student ID</th>
                                <th class="px-4 sm:px-6 py-4">Name</th>
                                <th class="px-4 sm:px-6 py-4">College</th>
                                <th class="px-4 sm:px-6 py-4">Year</th>
                                <th class="px-4 sm:px-6 py-4">Activity</th>
                                <th class="px-4 sm:px-6 py-4">Login</th>
                                <th class="px-4 sm:px-6 py-4">Logout</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="attendance-table-body">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-blue-50 transition-all duration-200">
                                    <td class="px-4 sm:px-6 py-4 font-medium text-gray-900">{{ $attendance->student_id }}</td>
                                    <td class="px-4 sm:px-6 py-4 flex items-center space-x-3">
                                        <img src="{{ $attendance->student && $attendance->student->user && $attendance->student->user->profile_picture ? asset('storage/' . $attendance->student->user->profile_picture) : asset('images/default-profile.png') }}" 
                                            alt="Profile Picture" 
                                            class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100 transition-transform duration-300 hover:scale-105" />
                                        <span class="font-medium">{{ $attendance->student->lname ?? '' }}, {{ $attendance->student->fname ?? '' }}</span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <span class="college-{{ $attendance->student->college ?? '' }} bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $attendance->student->college ?? '' }}</span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">{{ $attendance->student->year ?? '' }}</td>
                                    <td class="px-4 sm:px-6 py-4">
                                        @if(str_contains($attendance->activity, 'Borrow'))
                                            @php
                                                $parts = explode(':', $attendance->activity);
                                                    $activity = $parts[0];
                                                    $bookCode = $parts[1] ?? 'N/A';
                                            @endphp
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $activity }}: {{ $bookCode }}</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $attendance->activity ?? '' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">{{ \Carbon\Carbon::parse($attendance->login)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                                    <td class="px-4 sm:px-6 py-4">{{ $attendance->logout ? \Carbon\Carbon::parse($attendance->logout)->setTimezone('Asia/Manila')->format('h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 sm:px-6 py-6 text-center text-gray-500">No attendance logs yet.</td>
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
