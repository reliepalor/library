<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <div class="relative">
                        <!-- Dropdown Button -->
                        <button id="dropdownButton" class="flex items-center bg-gray-800 px-4 py-2 text-white rounded-lg text-bold hover:bg-gray-700 duration-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400" aria-haspopup="true" aria-expanded="false">
                            <span>Menu</span>
                            <svg class="ml-2 w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu" class="absolute right-0 mt-2 w-56 bg-white/90 backdrop-blur-md rounded-md shadow-lg py-2 z-10 hidden transform origin-top-right scale-y-0 transition-all duration-300">
                            <a href="{{ route('admin.attendance.index') }}" class="flex items-center px-4 py-2 text-gray-800 hover:bg-blue-100 hover:text-blue-600 transition-colors duration-200 opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">
                                <svg class="w-5 h-5 mr-2 text-gray-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Attendance
                            </a>
                            <a href="{{ route('admin.attendance.history') }}" class="flex items-center px-4 py-2 text-gray-800 hover:bg-blue-100 hover:text-blue-600 transition-colors duration-200 opacity-0 animate-fadeInUp" style="animation-delay: 0.2s;">
                                <svg class="w-5 h-5 mr-2 text-gray-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                View History
                            </a>
                            <a href="{{ route('admin.attendance.analytics') }}" class="flex items-center px-4 py-2 text-gray-800 hover:bg-blue-100 hover:text-blue-600 transition-colors duration-200 opacity-0 animate-fadeInUp" style="animation-delay: 0.3s;">
                                <svg class="w-5 h-5 mr-2 text-gray-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Analytics
                            </a>
                            
                        </div>
    </div>
</body>
</html>