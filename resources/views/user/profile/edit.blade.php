<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - User</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @vite('resources/css/app.css')
    <!-- Figtree Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32?text=CSU">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <x-header />
<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 bg-gradient-to-b from-white to-gray-100 min-h-screen">
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-10 space-y-10 transition duration-300 ease-in-out">
        
        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-blue-100 text-blue-800 rounded-lg flex items-center gap-2 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Profile updated successfully.</span>
            </div>
        @endif

        <!-- Profile Form -->
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PATCH')

            <!-- Header with Profile Picture and Name -->
            <div class="flex flex-col sm:flex-row items-center gap-6 sm:items-start">
                <div class="relative group">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}"
                         alt="Profile Picture"
                         class="w-28 h-28 rounded-full object-cover ring-4 ring-blue-200 shadow-md transition duration-300">

                    <label for="profile_picture"
                           class="absolute bottom-0 right-0 p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h6m6 0H5a2 2 0 00-2 2v6a2 2 0 002 2h6m6 0h-6m6 0a2 2 0 002-2V9a2 2 0 00-2-2m-6 12V7" />
                        </svg>
                        <input id="profile_picture" name="profile_picture" type="file" class="hidden" />
                    </label>
                </div>
                <div class="text-center sm:text-left space-y-1 w-full">
                    <div class="mt-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <!-- Add more fields for college, year, other_info if needed -->
                    </div>
                </div>
            </div>
            @error('profile_picture')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <!-- Save Button -->
            <div class="text-right">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-md transition duration-300">
                    Save Changes
                </button>
            </div>
        </form>

        <!-- Student Info Section -->
        @if ($user->student)
            <div class="bg-gray-50 rounded-xl shadow-inner p-6 sm:p-8 space-y-6 transition duration-300">
                <h3 class="text-xl font-semibold text-gray-800">Student Profile Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <label class="text-gray-600 font-medium">Student ID</label>
                        <p class="text-gray-900 font-semibold">{{ $user->student->student_id }}</p>
                    </div>
                    <div>
                        <label class="text-gray-600 font-medium">Full Name</label>
                        <p class="text-gray-900 font-semibold">{{ $user->student->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-gray-600 font-medium">College</label>
                        <p class="text-gray-900 font-semibold">{{ $user->student->college }}</p>
                    </div>
                    <div>
                        <label class="text-gray-600 font-medium">Year Level</label>
                        <p class="text-gray-900 font-semibold">{{ $user->student->year }}</p>
                    </div>
               
                    <div class="sm:col-span-2">
                        <label class="text-gray-600 font-medium mb-2 block">QR Code</label>
                        @if ($user->student->qr_code_path)
                            <img src="{{ asset('storage/' . $user->student->qr_code_path) }}"
                                 alt="Student QR Code"
                                 class="w-40 h-40 object-contain border border-gray-200 rounded-lg shadow-md hover:scale-105 transition duration-300" />
                        @else
                            <p class="text-gray-500">No QR code available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance History Section -->
            <div class="mt-10 bg-white rounded-xl shadow-inner p-6 sm:p-8 space-y-6 transition duration-300">
                <h3 class="text-xl font-semibold text-gray-800">Attendance History</h3>
                @if ($user->student->attendanceHistories->isEmpty())
                    <p class="text-gray-500">No attendance history available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left py-2 px-4 border-b border-gray-300">Date</th>
                                    <th class="text-left py-2 px-4 border-b border-gray-300">Time In - Time Out</th>
                                    <th class="text-left py-2 px-4 border-b border-gray-300">Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->student->attendanceHistories as $history)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-2 px-4 border-b border-gray-300">{{ $history->date->format('Y-m-d') }}</td>
                                        <td class="py-2 px-4 border-b border-gray-300">
                                            {{ $history->time_in ? $history->time_in->format('h:i A') : '-' }} - {{ $history->time_out ? $history->time_out->format('h:i A') : '-' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300">{{ $history->activity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</main>

</body>
</html>
